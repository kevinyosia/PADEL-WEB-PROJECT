@extends('layouts.app')

@section('content')
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
                    <span class="font-medium">{{ $transaction->reservation->coach->nama_coach ?? 'Tidak ada' }}</span>
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
                    <span class="text-red-600 font-semibold">{{ ucfirst(str_replace('_', ' ', $transaction->status_pembayaran)) }}</span>
                @endif
            </p>
        </div>

        <!-- Payment Button / Snap Container -->
        @if($transaction->status_pembayaran !== 'lunas')
        <div class="bg-white rounded-lg shadow-lg p-6">
            <button id="payButton" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-4 rounded-lg transition duration-200">
                Lanjutkan ke Pembayaran
            </button>
            <p class="text-center text-gray-500 text-sm mt-3">
                Klik tombol di atas untuk melanjutkan pembayaran melalui Midtrans Snap
            </p>
        </div>
        @else
        <div class="bg-green-50 border-l-4 border-green-500 p-6 rounded-lg">
            <div class="flex items-center">
                <svg class="w-8 h-8 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                <div>
                    <h3 class="text-lg font-semibold text-green-800">Pembayaran Berhasil</h3>
                    <p class="text-green-700">Terima kasih! Reservasi Anda telah dikonfirmasi.</p>
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
                        // Check payment status and redirect
                        checkPaymentStatus();
                    },
                    onPending: function(result) {
                        console.log('Payment pending:', result);
                        // Check payment status
                        checkPaymentStatus();
                    },
                    onError: function(result) {
                        console.log('Payment error:', result);
                        alert('Pembayaran gagal. Silakan coba lagi.');
                    },
                    onClose: function() {
                        console.log('Payment dialog closed');
                        // Check payment status when user closes the modal
                        checkPaymentStatus();
                    }
                });
            } else {
                alert('Gagal mendapatkan token pembayaran');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan. Silakan coba lagi.');
        });
    });

    function checkPaymentStatus() {
        const transactionId = {{ $transaction->id }};
        
        fetch(`/payment/${transactionId}/status`, {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'lunas') {
                alert('Pembayaran Anda berhasil! Terima kasih.');
                window.location.reload();
            }
        })
        .catch(error => console.error('Error checking status:', error));
    }
</script>
@endsection
