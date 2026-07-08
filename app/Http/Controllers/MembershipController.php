<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Membership;
use App\Models\MembershipPayment;
use App\Models\PointHistory;
use Illuminate\View\View;

class MembershipController extends Controller
{
    private const MEMBERSHIP_FEE = 100000;

    public function index(): View
    {
        $user = Auth::user();
        $membership = $user->membership;
        $isMember = $membership !== null;

        $pointHistories = collect();
        if ($isMember) {
            $pointHistories = PointHistory::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->take(20)
                ->get();
        }

        return view('user.membership.index', compact('isMember', 'membership', 'pointHistories'));
    }

    public function generateSnapToken(Request $request)
    {
        $user = Auth::user();

        if ($user->membership) {
            return response()->json(['error' => 'Anda sudah menjadi member.'], 422);
        }

        $orderId = 'MEM-' . $user->id . '-' . time();
        $amount = self::MEMBERSHIP_FEE;

        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $amount,
            ],
            'customer_details' => [
                'first_name' => $user->name ?? 'Customer',
                'email' => $user->email ?? 'noemail@example.com',
                'phone' => $user->phone ?? '08000000000',
            ],
            'item_details' => [
                [
                    'id' => 'membership-pass',
                    'price' => $amount,
                    'quantity' => 1,
                    'name' => 'Bandeja Member Pass - Lifetime Membership',
                ],
            ],
        ];

        try {
            $serverKey = trim((string) config('midtrans.server_key'));
            $apiUrl = 'https://app.sandbox.midtrans.com/snap/v1/transactions';

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $apiUrl);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Authorization: Basic ' . base64_encode($serverKey . ':'),
                'Accept: application/json',
            ]);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            if ($curlError) {
                throw new \Exception('CURL Error: ' . $curlError);
            }

            if ($httpCode !== 201 && $httpCode !== 200) {
                throw new \Exception('HTTP ' . $httpCode . ': ' . $response);
            }

            $responseData = json_decode((string) $response, true);
            if (!is_array($responseData) || empty($responseData['token'])) {
                throw new \Exception('No snap token in response');
            }

            MembershipPayment::create([
                'user_id' => $user->id,
                'order_id' => $orderId,
                'amount' => $amount,
                'status' => 'pending',
                'snap_token' => $responseData['token'],
            ]);

            return response()->json([
                'snap_token' => $responseData['token'],
                'client_key' => config('midtrans.client_key'),
            ]);
        } catch (\Exception $e) {
            \Log::error('Membership Midtrans error', [
                'user_id' => $user->id,
                'message' => $e->getMessage(),
            ]);

            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function complete(Request $request)
    {
        $user = Auth::user();
        $orderId = (string) $request->query('order_id', '');

        if (!preg_match('/^MEM-' . $user->id . '-\d+$/', $orderId)) {
            return redirect()->route('membership.index')->with('error', 'Order membership tidak valid.');
        }

        try {
            $statusData = $this->fetchMidtransStatus($orderId);
            if (is_array($statusData)) {
                $this->syncMembershipFromStatus(
                    $user->id,
                    $orderId,
                    $statusData['transaction_status'] ?? null,
                    $statusData['fraud_status'] ?? null,
                    (int) self::MEMBERSHIP_FEE
                );
            }
        } catch (\Exception $e) {
            \Log::warning('Membership complete check failed', [
                'user_id' => $user->id,
                'order_id' => $orderId,
                'error' => $e->getMessage(),
            ]);
        }

        if ($user->fresh()->membership) {
            return redirect()->route('membership.index')->with('success', 'Pembayaran berhasil. Akun Anda sekarang menjadi member.');
        }

        return redirect()->route('membership.index')->with('status', 'Pembayaran membership masih menunggu konfirmasi.');
    }

    private function syncMembershipFromStatus(int $userId, string $orderId, ?string $transactionStatus, ?string $fraudStatus, int $amount): void
    {
        $payment = MembershipPayment::firstOrCreate(
            ['order_id' => $orderId],
            [
                'user_id' => $userId,
                'amount' => $amount,
                'status' => 'pending',
            ]
        );

        if ($transactionStatus === 'capture' && ($fraudStatus === 'accept' || $fraudStatus === null)) {
            $payment->update(['status' => 'paid', 'paid_at' => now()]);
            Membership::firstOrCreate(
                ['user_id' => $userId],
                ['total_poin_aktif' => 0, 'total_poin_terpakai' => 0]
            );
            return;
        }

        if ($transactionStatus === 'settlement') {
            $payment->update(['status' => 'paid', 'paid_at' => now()]);
            Membership::firstOrCreate(
                ['user_id' => $userId],
                ['total_poin_aktif' => 0, 'total_poin_terpakai' => 0]
            );
            return;
        }

        if ($transactionStatus === 'pending') {
            $payment->update(['status' => 'pending']);
            return;
        }

        if (in_array($transactionStatus, ['deny', 'cancel'], true)) {
            $payment->update(['status' => 'failed']);
            return;
        }

        if ($transactionStatus === 'expire') {
            $payment->update(['status' => 'expired']);
            return;
        }

        if ($transactionStatus === 'refund') {
            $payment->update(['status' => 'refunded']);
        }
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
            \Log::warning('Membership status API non-2xx', [
                'order_id' => $orderId,
                'http_code' => $httpCode,
                'response' => $data,
            ]);
            return null;
        }

        return $data;
    }
}
