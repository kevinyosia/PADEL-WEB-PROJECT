<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaction;
use App\Models\Reservation;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Transaction as MidtransTransaction;

class PaymentController extends Controller
{
    public function __construct()
    {
        // Configure Midtrans
        Config::$serverKey = config('midtrans.server_key');
        Config::$clientKey = config('midtrans.client_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    /**
     * Generate Snap Token untuk pembayaran
     */
    public function generateSnapToken(Request $request)
    {
        $transactionId = $request->input('transaction_id');
        
        $transaction = Transaction::with('reservation.user', 'reservation.court')
            ->find($transactionId);

        if (!$transaction) {
            return response()->json(['error' => 'Transaction not found'], 404);
        }

        // Check ownership
        if ($transaction->reservation->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Prepare Midtrans params
        $params = [
            'transaction_details' => [
                'order_id' => 'ORD-' . $transaction->id . '-' . time(),
                'gross_amount' => $transaction->grand_total,
            ],
            'customer_details' => [
                'first_name' => Auth::user()->name,
                'email' => Auth::user()->email,
                'phone' => Auth::user()->phone,
            ],
            'item_details' => [
                [
                    'id' => 'court-' . $transaction->reservation->court->id,
                    'price' => $transaction->total_harga_lapangan,
                    'quantity' => 1,
                    'name' => 'Sewa Lapangan: ' . $transaction->reservation->court->nama_lapangan,
                ],
            ],
        ];

        // Add coach if selected
        if ($transaction->reservation->coach_id && $transaction->total_harga_coach > 0) {
            $params['item_details'][] = [
                'id' => 'coach-' . $transaction->reservation->coach_id,
                'price' => $transaction->total_harga_coach,
                'quantity' => 1,
                'name' => 'Coach Service',
            ];
        }

        // Add equipment/products if selected
        if ($transaction->total_harga_perlengkapan > 0) {
            $params['item_details'][] = [
                'id' => 'equipment',
                'price' => $transaction->total_harga_perlengkapan,
                'quantity' => 1,
                'name' => 'Perlengkapan & Produk',
            ];
        }

        try {
            $snapToken = Snap::getSnapToken($params);
            
            // Save snap token to transaction
            $transaction->update([
                'snap_token' => $snapToken,
                'status_pembayaran' => 'pending',
            ]);

            return response()->json([
                'snap_token' => $snapToken,
                'client_key' => config('midtrans.client_key'),
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Payment page (display Snap)
     */
    public function paymentPage($transactionId)
    {
        $transaction = Transaction::with('reservation.user', 'reservation.court')
            ->find($transactionId);

        if (!$transaction) {
            abort(404, 'Transaction not found');
        }

        if ($transaction->reservation->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        return view('user.payment.index', compact('transaction'));
    }

    /**
     * Webhook callback dari Midtrans
     */
    public function webhook(Request $request)
    {
        $serverKey = config('midtrans.server_key');
        $hashed = hash('sha512', $request->order_id . $request->status_code . $request->gross_amount . $serverKey);

        // Verify signature
        if ($hashed !== $request->signature_key) {
            return response()->json(['error' => 'Invalid signature'], 403);
        }

        $orderId = $request->order_id; // Format: ORD-{transactionId}-{timestamp}
        
        // Extract transaction ID dari order_id
        preg_match('/ORD-(\d+)-/', $orderId, $matches);
        $transactionId = $matches[1] ?? null;

        if (!$transactionId) {
            return response()->json(['error' => 'Invalid order ID'], 400);
        }

        $transaction = Transaction::find($transactionId);
        if (!$transaction) {
            return response()->json(['error' => 'Transaction not found'], 404);
        }

        // Update transaction status based on Midtrans response
        $transactionStatus = $request->transaction_status;
        $fraudStatus = $request->fraud_status ?? null;

        if ($transactionStatus === 'capture') {
            if ($fraudStatus === 'challenge') {
                $transaction->update(['status_pembayaran' => 'pending']);
            } else if ($fraudStatus === 'accept') {
                $transaction->update(['status_pembayaran' => 'lunas']);
                // Update reservation status
                $transaction->reservation->update(['status_reservasi' => 'completed']);
            }
        } else if ($transactionStatus === 'settlement') {
            $transaction->update(['status_pembayaran' => 'lunas']);
            $transaction->reservation->update(['status_reservasi' => 'completed']);
        } else if ($transactionStatus === 'pending') {
            $transaction->update(['status_pembayaran' => 'belum_lunas']);
        } else if ($transactionStatus === 'deny') {
            $transaction->update(['status_pembayaran' => 'belum_lunas']);
        } else if ($transactionStatus === 'cancel' || $transactionStatus === 'expire') {
            $transaction->update(['status_pembayaran' => 'belum_lunas']);
            $transaction->reservation->update(['status_reservasi' => 'cancelled']);
        } else if ($transactionStatus === 'refund') {
            $transaction->update(['status_pembayaran' => 'refund']);
        }

        return response()->json(['status' => 'success']);
    }

    /**
     * Check payment status
     */
    public function checkStatus($transactionId)
    {
        $transaction = Transaction::find($transactionId);

        if (!$transaction) {
            return response()->json(['error' => 'Transaction not found'], 404);
        }

        if ($transaction->reservation->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        try {
            $status = MidtransTransaction::status($transaction->snap_token ?? 'ORD-' . $transaction->id);

            $midtransStatus = is_array($status) ? ($status['transaction_status'] ?? null) : ($status->transaction_status ?? null);

            return response()->json([
                'status' => $transaction->status_pembayaran,
                'midtrans_status' => $midtransStatus,
                'transaction' => $transaction,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
