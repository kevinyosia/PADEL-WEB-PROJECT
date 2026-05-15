@extends('layouts.user')

@section('content')
<style>
    #payButton:hover {
        background-color: #4338ca !important;
    }
    #payButton:active {
        opacity: 0.9;
    }
</style>
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-2xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Pembayaran Reservasi</h1>
            <p class="mt-2 text-gray-600">Selesaikan pembayaran untuk reservasi Anda</p>
        </div>

        <!-- Order Details Card -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Detail Reservasi</h2>
            
            <div class="space-y-3">
                <div class="flex justify-between items-center py-2 border-b">
                    <span class="text-gray-600">Lapangan</span>
                    <span class="font-medium">{{ $transaction->reservation->court->nama_lapangan }}</span>
                </div>
                
                @if($transaction->reservation->coach)
                <div class="flex justify-between items-center py-2 border-b">
                    <span class="text-gray-600">Coach</span>
                    <span class="font-medium">{{ $transaction->reservation->coach->user->name ?? 'Coach terpilih' }}</span>
                </div>
                @endif
                
                <div class="flex justify-between items-center py-2 border-b">
                    <span class="text-gray-600">Tanggal Reservasi</span>
                    <span class="font-medium">{{ \Carbon\Carbon::parse($transaction->reservation->tanggal_reservasi)->format('d/m/Y H:i') }}</span>
                </div>
            </div>

            <div class="mt-6 space-y-2">
                <div class="flex justify-between items-center py-3 border-t-2">
                    <span class="text-gray-700">Sewa Lapangan</span>
                    <span class="font-medium">Rp {{ number_format($transaction->total_harga_lapangan, 0, ',', '.') }}</span>
                </div>
                
                @if($transaction->total_harga_coach > 0)
                <div class="flex justify-between items-center py-2">
                    <span class="text-gray-700">Coach Service</span>
                    <span class="font-medium">Rp {{ number_format($transaction->total_harga_coach, 0, ',', '.') }}</span>
                </div>
                @endif
                
                @if($transaction->total_harga_perlengkapan > 0)
                <div class="flex justify-between items-center py-2">
                    <span class="text-gray-700">Perlengkapan & Produk</span>
                    <span class="font-medium">Rp {{ number_format($transaction->total_harga_perlengkapan, 0, ',', '.') }}</span>
                </div>
                @endif

                @if(($transaction->potongan_poin ?? 0) > 0)
                <div class="flex justify-between items-center py-2">
                    <span class="text-gray-700">Potongan Poin Member</span>
                    <span class="font-medium text-green-700">- Rp {{ number_format($transaction->potongan_poin, 0, ',', '.') }}</span>
                </div>
                @endif
                
                <div class="flex justify-between items-center py-3 border-t-2 bg-indigo-50 px-3 rounded mt-4">
                    <span class="text-lg font-bold text-gray-900">Total Pembayaran</span>
                    <span class="text-2xl font-bold text-indigo-600">Rp {{ number_format($transaction->grand_total, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <!-- Payment Status -->
        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6 rounded">
            <p class="text-sm text-gray-700">
                <span class="font-semibold">Status Pembayaran:</span> 
                @if($transaction->status_pembayaran === 'lunas')
                    <span class="text-green-600 font-semibold">Sudah Lunas</span>
                @elseif($transaction->status_pembayaran === 'pending')
                    <span class="text-yellow-600 font-semibold">Menunggu Pembayaran</span>
                @else
                    <span class="text-red-600 font-semibold">{{ $transaction->status_pembayaran }}</span>
                @endif
            </p>
            <p class="text-xs text-gray-500 mt-2">Debug: Status = "{{ $transaction->status_pembayaran }}" | Condition Result = {{ $transaction->status_pembayaran !== 'lunas' ? 'TRUE' : 'FALSE' }}</p>
        </div>

        <!-- Payment Button / Snap Container -->
        @if($transaction->status_pembayaran !== 'lunas')
        <div style="background: white; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); padding: 24px; margin-top: 24px;">
            <button id="payButton" style="width: 100%; background-color: #4f46e5; color: white; font-weight: bold; padding: 12px 16px; border-radius: 8px; border: none; cursor: pointer; font-size: 16px; transition: background-color 0.2s;">
                Lanjutkan ke Pembayaran
            </button>
            <p style="text-align: center; color: #6b7280; font-size: 14px; margin-top: 12px;">
                Klik tombol di atas untuk melanjutkan pembayaran melalui Midtrans Snap
            </p>
        </div>
        @else
        <div style="background: #f0fdf4; border-left: 4px solid #22c55e; padding: 24px; border-radius: 8px; margin-top: 24px;">
            <div style="display: flex; align-items: center;">
                <svg style="width: 32px; height: 32px; color: #22c55e; margin-right: 12px;" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                <div>
                    <h3 style="font-size: 18px; font-weight: 600; color: #166534;">Pembayaran Berhasil</h3>
                    <p style="color: #15803d;">Terima kasih! Reservasi Anda telah dikonfirmasi.</p>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Midtrans Snap Script -->
<script src="{{ config('midtrans.snap_url') }}"></script>
<script>
    document.getElementById('payButton')?.addEventListener('click', function() {
        const transactionId = {{ $transaction->id }};
        
        // Get snap token from server
        fetch('{{ route("payment.snap-token") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                transaction_id: transactionId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.snap_token) {
                // Trigger Snap payment modal
                snap.pay(data.snap_token, {
                    onSuccess: function(result) {
                        console.log('Payment success:', result);
                        const orderId = encodeURIComponent(result?.order_id || '');
                        window.location.href = `/payment/${transactionId}/complete?order_id=${orderId}`;
                    },
                    onPending: function(result) {
                        console.log('Payment pending:', result);
                        const orderId = result?.order_id || '';
                        checkPaymentStatus(orderId);
                        alert('Pembayaran Anda sedang menunggu. Silakan selesaikan pembayaran sesuai instruksi VA.');
                    },
                    onError: function(result) {
                        console.log('Payment error:', result);
                        alert('Pembayaran gagal. Silakan coba lagi.');
                    },
                    onClose: function() {
                        console.log('Payment dialog closed');
                    }
                });
            } else {
                console.error('Error response:', data);
                alert('Gagal mendapatkan token pembayaran: ' + (data.error || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan. Silakan coba lagi.');
        });
    });

    function checkPaymentStatus(orderId = '') {
        const transactionId = {{ $transaction->id }};
        const query = orderId ? `?order_id=${encodeURIComponent(orderId)}` : '';

        fetch(`/payment/${transactionId}/status${query}`, {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'lunas') {
                const encodedOrderId = encodeURIComponent(orderId || data?.transaction?.midtrans_order_id || '');
                window.location.href = `/payment/${transactionId}/complete?order_id=${encodedOrderId}`;
            }
        })
        .catch(error => console.error('Error checking status:', error));
    }

    @if($transaction->status_pembayaran === 'pending')
    setInterval(() => checkPaymentStatus(), 10000);
    @endif
</script>
@endsection
