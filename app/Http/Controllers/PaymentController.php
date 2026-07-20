<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Membership;
use App\Models\MembershipPayment;
use App\Models\Transaction;
use App\Models\PointHistory;
use Midtrans\Config;

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
        
        // Disable SSL verification for localhost/sandbox testing
        Config::$curlOptions[CURLOPT_SSL_VERIFYHOST] = 0;
        Config::$curlOptions[CURLOPT_SSL_VERIFYPEER] = false;
    }

    /**
     * Generate Snap Token untuk pembayaran
     */
    public function generateSnapToken(Request $request)
    {
        $transactionId = $request->input('transaction_id');
        
        $transaction = Transaction::with('reservation.user', 'reservation.court', 'reservation.coach.user')
            ->find($transactionId);

        if (!$transaction) {
            return response()->json(['error' => 'Transaction not found'], 404);
        }

        // Check ownership
        if ($transaction->reservation->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Prepare Midtrans params
        $orderId = 'ORD-' . $transaction->id . '-' . time();

        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => (int) $transaction->grand_total,
            ],
            'customer_details' => [
                'first_name' => Auth::user()->name ?? 'Customer',
                'email' => Auth::user()->email ?? 'noemail@example.com',
                'phone' => Auth::user()->phone ?? '08000000000',
            ],
            'item_details' => [
                [
                    'id' => 'court-' . $transaction->reservation->court->id,
                    'price' => (int) $transaction->total_harga_lapangan,
                    'quantity' => 1,
                    'name' => 'Sewa Lapangan: ' . $transaction->reservation->court->nama_lapangan,
                ],
            ],
        ];

        // Add coach if selected
        if ($transaction->reservation->coach_id && $transaction->total_harga_coach > 0) {
            $params['item_details'][] = [
                'id' => 'coach-' . $transaction->reservation->coach_id,
                'price' => (int) $transaction->total_harga_coach,
                'quantity' => 1,
                'name' => 'Coach Service: ' . ($transaction->reservation->coach?->user?->name ?? 'Coach'),
            ];
        }

        // Add equipment/products if selected
        if ($transaction->total_harga_perlengkapan > 0) {
            $params['item_details'][] = [
                'id' => 'equipment',
                'price' => (int) $transaction->total_harga_perlengkapan,
                'quantity' => 1,
                'name' => 'Perlengkapan & Produk',
            ];
        }

        if (($transaction->potongan_poin ?? 0) > 0) {
            $params['item_details'][] = [
                'id' => 'member-point-discount',
                'price' => -1 * (int) $transaction->potongan_poin,
                'quantity' => 1,
                'name' => 'Potongan Poin Member',
            ];
        }

        try {
            \Log::info('Generating Snap Token', [
                'transaction_id' => $transaction->id,
                'grand_total' => $transaction->grand_total,
                'params' => $params,
                'config_server_key' => Config::$serverKey,
            ]);
            
            // Try raw CURL with better debugging
            $serverKey = trim(config('midtrans.server_key'));
            $apiUrl = 'https://app.sandbox.midtrans.com/snap/v1/transactions';
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $apiUrl);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            
            // Format: base64(serverKey:)
            $auth = base64_encode($serverKey . ':');
            
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Authorization: Basic ' . $auth,
                'Accept: application/json',
            ]);
            
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);
            
            \Log::info('Raw CURL Response', [
                'http_code' => $httpCode,
                'response' => $response,
                'curl_error' => $curlError,
            ]);
            
            if ($curlError) {
                throw new \Exception('CURL Error: ' . $curlError);
            }
            
            if ($httpCode !== 201 && $httpCode !== 200) {
                throw new \Exception('HTTP ' . $httpCode . ': ' . $response);
            }
            
            $responseData = json_decode($response, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Invalid JSON response: ' . json_last_error_msg());
            }
            
            $snapToken = $responseData['token'] ?? null;
            
            if (!$snapToken) {
                \Log::error('No token in response', ['response' => $responseData]);
                throw new \Exception('No snap token in response: ' . json_encode($responseData));
            }
            
            \Log::info('Snap Token Generated Successfully', [
                'transaction_id' => $transaction->id,
                'snap_token_length' => strlen($snapToken),
            ]);
            
            // Save snap token to transaction
            $transaction->update([
                'snap_token' => $snapToken,
                'status_pembayaran' => 'pending',
                'midtrans_order_id' => $orderId,
            ]);

            return response()->json([
                'snap_token' => $snapToken,
                'client_key' => config('midtrans.client_key'),
            ]);
        } catch (\Exception $e) {
            \Log::error('Midtrans Error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Payment page (display Snap)
     */
    public function paymentPage($transactionId)
    {
        $transaction = Transaction::with('reservation.user', 'reservation.court', 'reservation.coach.user')
            ->find($transactionId);

        if (!$transaction) {
            abort(404, 'Transaction not found');
        }

        if ($transaction->reservation->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        if ($transaction->status_pembayaran === 'pending' && !empty($transaction->midtrans_order_id)) {
            try {
                $status = $this->fetchMidtransStatus($transaction->midtrans_order_id);
                if ($status !== null) {
                    $this->syncStatusFromMidtrans(
                        $transaction,
                        $status['transaction_status'] ?? null,
                        $status['fraud_status'] ?? null
                    );
                }
                $transaction->refresh();
            } catch (\Exception $e) {
                \Log::warning('Payment page status sync failed', [
                    'transaction_id' => $transaction->id,
                    'order_id' => $transaction->midtrans_order_id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return view('user.payment.index', compact('transaction'));
    }

    /**
     * Webhook callback dari Midtrans
     */
    public function webhook(Request $request)
    {
        \Log::info('Midtrans webhook received', [
            'order_id' => $request->order_id,
            'transaction_status' => $request->transaction_status,
            'status_code' => $request->status_code,
            'gross_amount' => $request->gross_amount,
            'fraud_status' => $request->fraud_status,
        ]);

        $serverKey = config('midtrans.server_key');
        $hashed = hash('sha512', $request->order_id . $request->status_code . $request->gross_amount . $serverKey);

        // Verify signature
        if ($hashed !== $request->signature_key) {
            \Log::warning('Midtrans webhook signature mismatch', [
                'order_id' => $request->order_id,
                'provided_signature' => $request->signature_key,
            ]);

            // Midtrans dashboard notification test can send a synthetic payload.
            // Keep the endpoint reachable in sandbox/local so the URL test can pass,
            // but do not process any transaction update without a valid signature.
            if (app()->environment(['local', 'testing'])) {
                return response()->json(['status' => 'ok', 'message' => 'Signature mismatch ignored in local/testing environment']);
            }

            return response()->json(['error' => 'Invalid signature'], 403);
        }

        $orderId = $request->order_id; // Format: ORD-{transactionId}-{timestamp}

        if (str_starts_with((string) $orderId, 'MEM-')) {
            return $this->handleMembershipWebhook($request, (string) $orderId);
        }
        
        // Extract transaction ID dari order_id
        preg_match('/ORD-(\d+)-/', $orderId, $matches);
        $transactionId = $matches[1] ?? null;

        if (!$transactionId) {
            \Log::warning('Midtrans webhook invalid order id format', [
                'order_id' => $orderId,
            ]);
            return response()->json(['error' => 'Invalid order ID'], 400);
        }

        $transaction = Transaction::find($transactionId);
        if (!$transaction) {
            \Log::warning('Midtrans webhook transaction not found', [
                'transaction_id' => $transactionId,
                'order_id' => $orderId,
            ]);
            return response()->json(['error' => 'Transaction not found'], 404);
        }

        // Update transaction status based on Midtrans response
        $transactionStatus = $request->transaction_status;
        $fraudStatus = $request->fraud_status ?? null;

        $transaction->update([
            'midtrans_order_id' => $orderId,
        ]);

        $this->syncStatusFromMidtrans($transaction, $transactionStatus, $fraudStatus);

        \Log::info('Midtrans webhook transaction updated', [
            'transaction_id' => $transaction->id,
            'status_pembayaran' => $transaction->fresh()->status_pembayaran,
            'transaction_status' => $transactionStatus,
        ]);

        return response()->json(['status' => 'success']);
    }

    /**
     * Check payment status
     */
    public function checkStatus(Request $request, $transactionId)
    {
        $transaction = Transaction::with('reservation')->find($transactionId);

        if (!$transaction) {
            return response()->json(['error' => 'Transaction not found'], 404);
        }

        if ($transaction->reservation->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $orderId = (string) $request->query('order_id', '');
        if ($orderId === '') {
            $orderId = (string) ($transaction->midtrans_order_id ?? '');
        }

        if ($orderId && str_starts_with($orderId, 'ORD-' . $transaction->id . '-')) {
            try {
                $status = $this->fetchMidtransStatus($orderId);
                if ($status !== null) {
                    $this->syncStatusFromMidtrans(
                        $transaction,
                        $status['transaction_status'] ?? null,
                        $status['fraud_status'] ?? null
                    );
                }
            } catch (\Exception $e) {
                \Log::warning('Midtrans status check failed', [
                    'transaction_id' => $transaction->id,
                    'order_id' => $orderId,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $transaction->refresh();

        return response()->json([
            'status' => $transaction->status_pembayaran,
            'transaction' => $transaction,
        ]);
    }

    /**
     * Redirect flow after successful Snap callback
     */
    public function complete(Request $request, $transactionId)
    {
        $transaction = Transaction::with('reservation')->find($transactionId);

        if (!$transaction) {
            return redirect()->route('courts.index')->with('error', 'Transaksi tidak ditemukan.');
        }

        if ($transaction->reservation->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $orderId = (string) $request->query('order_id', '');
        if ($orderId === '') {
            $orderId = (string) ($transaction->midtrans_order_id ?? '');
        }

        if ($orderId !== '' && str_starts_with($orderId, 'ORD-' . $transaction->id . '-')) {
            try {
                $status = $this->fetchMidtransStatus($orderId);
                if ($status !== null) {
                    $this->syncStatusFromMidtrans(
                        $transaction,
                        $status['transaction_status'] ?? null,
                        $status['fraud_status'] ?? null
                    );
                }
                $transaction->refresh();
            } catch (\Exception $e) {
                \Log::warning('Payment complete check failed', [
                    'transaction_id' => $transaction->id,
                    'order_id' => $orderId,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        if ($transaction->status_pembayaran === 'lunas') {
            return redirect()->route('courts.index')->with('success', 'Pembayaran berhasil. Reservasi Anda sudah dikonfirmasi.');
        }

        return redirect()->route('payment.page', $transaction->id)
            ->with('status', 'Pembayaran Anda masih menunggu konfirmasi. Silakan cek status beberapa saat lagi.');
    }

    /**
     * Cancel unpaid reservation when user leaves payment flow.
     */
    public function abandon(Request $request, $transactionId)
    {
        $transaction = Transaction::with('reservation.user.membership')->find($transactionId);

        if (! $transaction) {
            return response()->json(['error' => 'Transaction not found'], 404);
        }

        if ($transaction->reservation->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if ($transaction->status_pembayaran === 'lunas') {
            return response()->json(['status' => 'skipped', 'message' => 'Transaction already paid']);
        }

        DB::transaction(function () use ($transaction) {
            $locked = Transaction::with('reservation.user.membership')
                ->whereKey($transaction->id)
                ->lockForUpdate()
                ->first();

            if (! $locked || $locked->status_pembayaran === 'lunas') {
                return;
            }

            $locked->update([
                'status_pembayaran' => 'belum_lunas',
            ]);

            $this->releaseReservationForUnpaidTransaction($locked);
        });

        return response()->json(['status' => 'success']);
    }

    private function syncStatusFromMidtrans(Transaction $transaction, ?string $transactionStatus, ?string $fraudStatus): void
    {
        if ($transactionStatus === 'capture') {
            if ($fraudStatus === 'challenge') {
                $transaction->update(['status_pembayaran' => 'pending']);
                return;
            }

            if ($fraudStatus === 'accept' || $fraudStatus === null) {
                $this->applyPointDiscountIfEligible($transaction);
                $transaction->update(['status_pembayaran' => 'lunas']);
                $transaction->reservation->update(['status_reservasi' => 'completed']);
                $this->grantMembershipRewardIfEligible($transaction);
                return;
            }
        }

        if ($transactionStatus === 'settlement') {
            $this->applyPointDiscountIfEligible($transaction);
            $transaction->update(['status_pembayaran' => 'lunas']);
            $transaction->reservation->update(['status_reservasi' => 'completed']);
            $this->grantMembershipRewardIfEligible($transaction);
            return;
        }

        if ($transactionStatus === 'pending') {
            $transaction->update(['status_pembayaran' => 'pending']);
            return;
        }

        if (in_array($transactionStatus, ['deny', 'cancel', 'expire'], true)) {
            $transaction->update(['status_pembayaran' => 'belum_lunas']);
            if (in_array($transactionStatus, ['cancel', 'expire'], true)) {
                $this->releaseReservationForUnpaidTransaction($transaction);
            }
            return;
        }

        if ($transactionStatus === 'refund') {
            $transaction->update(['status_pembayaran' => 'refund']);
        }
    }

    /**
     * Release reservation and refund used points for unpaid/cancelled transactions.
     */
    private function releaseReservationForUnpaidTransaction(Transaction $transaction): void
    {
        $transaction->loadMissing('reservation.user.membership');

        $reservation = $transaction->reservation;
        if (! $reservation) {
            return;
        }

        if ($reservation->status_reservasi !== 'cancelled') {
            $reservation->update(['status_reservasi' => 'cancelled']);
        }

        $usedPoints = (int) ($transaction->potongan_poin ?? 0);
        if ($usedPoints <= 0) {
            return;
        }

        $user = $reservation->user;
        $membership = $user?->membership;
        if (! $user || ! $membership) {
            return;
        }

        $redeemNote = 'Penukaran poin untuk transaksi #' . $transaction->id;
        $wasDeducted = PointHistory::query()
            ->where('user_id', $user->id)
            ->where('keterangan', $redeemNote)
            ->exists();

        if (! $wasDeducted) {
            return;
        }

        $refundNote = 'Pengembalian poin dari transaksi #' . $transaction->id;
        $alreadyRefunded = PointHistory::query()
            ->where('user_id', $user->id)
            ->where('keterangan', $refundNote)
            ->exists();

        if ($alreadyRefunded) {
            return;
        }

        $membership->increment('total_poin_aktif', $usedPoints);

        $remainingUsed = max(0, (int) $membership->total_poin_terpakai - $usedPoints);
        $membership->update(['total_poin_terpakai' => $remainingUsed]);

        PointHistory::create([
            'user_id' => $user->id,
            'jumlah_poin' => $usedPoints,
            'keterangan' => $refundNote,
        ]);
    }

    private function applyPointDiscountIfEligible(Transaction $transaction): void
    {
        $usedPoints = (int) ($transaction->potongan_poin ?? 0);
        if ($usedPoints <= 0) {
            return;
        }

        $transaction->loadMissing('reservation.user.membership');

        $user = $transaction->reservation?->user;
        if (! $user) {
            return;
        }

        $note = 'Penukaran poin untuk transaksi #' . $transaction->id;
        $alreadyDeducted = PointHistory::query()
            ->where('user_id', $user->id)
            ->where('keterangan', $note)
            ->exists();

        if ($alreadyDeducted) {
            return;
        }

        DB::transaction(function () use ($user, $transaction, $usedPoints, $note) {
            $membership = Membership::query()
                ->where('user_id', $user->id)
                ->lockForUpdate()
                ->first();

            if (! $membership) {
                \Log::warning('Point discount skipped because membership was not found', [
                    'transaction_id' => $transaction->id,
                    'user_id' => $user->id,
                    'used_points' => $usedPoints,
                ]);
                return;
            }

            if ((int) $membership->total_poin_aktif < $usedPoints) {
                \Log::warning('Point discount skipped because active points are insufficient', [
                    'transaction_id' => $transaction->id,
                    'user_id' => $user->id,
                    'used_points' => $usedPoints,
                    'active_points' => (int) $membership->total_poin_aktif,
                ]);
                return;
            }

            $membership->decrement('total_poin_aktif', $usedPoints);
            $membership->increment('total_poin_terpakai', $usedPoints);

            PointHistory::create([
                'user_id' => $user->id,
                'jumlah_poin' => -$usedPoints,
                'keterangan' => $note,
            ]);
        });
    }

    private function grantMembershipRewardIfEligible(Transaction $transaction): void
    {
        $transaction->loadMissing('reservation.user.membership');

        $user = $transaction->reservation?->user;
        $membership = $user?->membership;

        if (!$user || !$membership) {
            return;
        }

        $points = (int) floor(((int) $transaction->grand_total) * 0.08);
        if ($points <= 0) {
            return;
        }

        $note = 'Cashback 8% dari transaksi #' . $transaction->id;

        $alreadyRewarded = PointHistory::query()
            ->where('user_id', $user->id)
            ->where('keterangan', $note)
            ->exists();

        if ($alreadyRewarded) {
            return;
        }

        $membership->increment('total_poin_aktif', $points);

        PointHistory::create([
            'user_id' => $user->id,
            'jumlah_poin' => $points,
            'keterangan' => $note,
        ]);

        \Log::info('Membership points rewarded', [
            'transaction_id' => $transaction->id,
            'user_id' => $user->id,
            'points' => $points,
        ]);
    }

    private function fetchMidtransStatus(string $orderId): ?array
    {
        $serverKey = trim((string) config('midtrans.server_key'));
        $baseUrl = config('midtrans.is_production')
            ? 'https://api.midtrans.com/v2/'
            : 'https://api.sandbox.midtrans.com/v2/';

        $statusUrl = $baseUrl . rawurlencode($orderId) . '/status';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $statusUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/json',
            'Authorization: Basic ' . base64_encode($serverKey . ':'),
        ]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            throw new \Exception('CURL Error status: ' . $curlError);
        }

        $data = json_decode((string) $response, true);
        if (!is_array($data)) {
            throw new \Exception('Invalid Midtrans status response');
        }

        if ($httpCode < 200 || $httpCode >= 300) {
            \Log::warning('Midtrans status API non-2xx', [
                'order_id' => $orderId,
                'http_code' => $httpCode,
                'response' => $data,
            ]);
            return null;
        }

        return $data;
    }

    private function handleMembershipWebhook(Request $request, string $orderId)
    {
        preg_match('/^MEM-(\d+)-/', $orderId, $matches);
        $userId = isset($matches[1]) ? (int) $matches[1] : null;

        if (!$userId) {
            \Log::warning('Membership webhook invalid order id format', [
                'order_id' => $orderId,
            ]);
            return response()->json(['error' => 'Invalid membership order id'], 400);
        }

        $amount = (int) round((float) $request->gross_amount);

        $payment = MembershipPayment::firstOrCreate(
            ['order_id' => $orderId],
            [
                'user_id' => $userId,
                'amount' => $amount,
                'status' => 'pending',
            ]
        );

        $transactionStatus = $request->transaction_status;
        $fraudStatus = $request->fraud_status ?? null;

        if ($transactionStatus === 'capture' && ($fraudStatus === 'accept' || $fraudStatus === null)) {
            $payment->update(['status' => 'paid', 'paid_at' => now()]);
            Membership::firstOrCreate(
                ['user_id' => $userId],
                ['total_poin_aktif' => 0, 'total_poin_terpakai' => 0]
            );
        } elseif ($transactionStatus === 'settlement') {
            $payment->update(['status' => 'paid', 'paid_at' => now()]);
            Membership::firstOrCreate(
                ['user_id' => $userId],
                ['total_poin_aktif' => 0, 'total_poin_terpakai' => 0]
            );
        } elseif ($transactionStatus === 'pending') {
            $payment->update(['status' => 'pending']);
        } elseif (in_array($transactionStatus, ['deny', 'cancel'], true)) {
            $payment->update(['status' => 'failed']);
        } elseif ($transactionStatus === 'expire') {
            $payment->update(['status' => 'expired']);
        } elseif ($transactionStatus === 'refund') {
            $payment->update(['status' => 'refunded']);
        }

        \Log::info('Membership webhook handled', [
            'order_id' => $orderId,
            'user_id' => $userId,
            'status' => $transactionStatus,
        ]);

        return response()->json(['status' => 'success']);
    }
}
