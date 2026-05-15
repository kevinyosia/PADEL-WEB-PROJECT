@extends('layouts.user')
@section('title', 'Membership — Bandeja Padel Arena')

@push('styles')
<style>
    :root {
        --green-deep: #2D4A1E; --green-mid: #3A5C28; --green-light: #4A7035;
        --cream-bg: #EDE8D8; --cream-card: #F5F1E6; --cream-white: #FAFAF5;
        --text-dark: #1A1A0F; --text-muted: #6B6B5A; --gold: #C8922A;
    }
    .page-wrap { min-height: 100vh; background: var(--cream-bg); }

    /* ─── NON-MEMBER ─── */
    .nonmember-wrap { padding: 40px 40px; }
    .nm-eyebrow { font-size: 11px; font-weight: 700; letter-spacing: .12em; text-transform: uppercase; color: var(--text-muted); margin-bottom: 12px; }
    .nm-title {
        font-family: 'DM Serif Display', serif;
        font-size: 44px; line-height: 1.15; color: var(--text-dark); margin-bottom: 14px;
    }
    .nm-title em { font-style: italic; color: var(--green-mid); }
    .nm-sub { font-size: 14px; color: var(--text-muted); line-height: 1.65; max-width: 520px; margin-bottom: 40px; }

    .pricing-cards { display: flex; gap: 20px; align-items: stretch; }
    .price-card {
        flex: 1; background: var(--cream-card); border-radius: 20px;
        padding: 28px 24px; border: 1px solid rgba(0,0,0,0.07);
        display: flex; flex-direction: column; gap: 14px;
    }
    .price-card.featured {
        background: var(--green-deep); color: #fff;
        border-color: var(--green-deep);
    }
    .card-icon-lg { font-size: 26px; }
    .card-tag {
        font-size: 10px; font-weight: 800; text-transform: uppercase;
        letter-spacing: .1em; color: var(--text-muted);
    }
    .price-card.featured .card-tag { color: rgba(255,255,255,0.55); }
    .card-name {
        font-family: 'DM Serif Display', serif;
        font-size: 22px; color: var(--text-dark);
    }
    .price-card.featured .card-name { color: #fff; }
    .card-tagline { font-size: 12px; color: var(--text-muted); line-height: 1.5; }
    .price-card.featured .card-tagline { color: rgba(255,255,255,0.6); }
    .card-price {
        font-family: 'DM Serif Display', serif;
        font-size: 32px; color: var(--text-dark);
    }
    .price-card.featured .card-price { color: #fff; }
    .card-price-note { font-size: 11px; color: var(--text-muted); margin-top: -8px; }
    .price-card.featured .card-price-note { color: rgba(255,255,255,0.5); }
    .card-feature {
        display: flex; align-items: center; gap: 8px;
        font-size: 13px; color: var(--text-muted);
    }
    .price-card.featured .card-feature { color: rgba(255,255,255,0.8); }
    .card-feature-icon { color: var(--green-light); font-size: 14px; }
    .price-card.featured .card-feature-icon { color: #A8D898; }
    .card-spacer { flex: 1; }
    .join-btn {
        display: block; width: 100%; padding: 13px;
        background: #fff; color: var(--green-deep);
        border: none; border-radius: 50px;
        font-size: 14px; font-weight: 800; font-family: 'Figtree', sans-serif;
        cursor: pointer; transition: all .18s; text-align: center;
        text-decoration: none;
    }
    .join-btn:hover { background: #F5F1E6; }
    .join-btn.outline {
        background: transparent; color: var(--text-muted);
        border: 1.5px solid rgba(0,0,0,0.12);
    }
    .join-btn.outline:hover { background: #E8E3D3; }

    /* ─── MEMBER ─── */
    .member-wrap { padding: 28px 32px; }

    .points-hero {
        background: var(--green-deep); border-radius: 20px;
        padding: 32px 36px; color: #fff; margin-bottom: 28px;
        position: relative; overflow: hidden;
    }
    .points-hero::before {
        content: ''; position: absolute; inset: 0;
        background: radial-gradient(ellipse at 80% 50%, rgba(255,255,255,0.06) 0%, transparent 60%);
    }
    .hero-badge {
        display: inline-flex; align-items: center; gap: 5px;
        background: rgba(255,255,255,0.15); border-radius: 20px;
        padding: 5px 12px; font-size: 11px; font-weight: 700;
        text-transform: uppercase; letter-spacing: .08em; margin-bottom: 16px;
    }
    .hero-badge::before { content: '●'; color: #A8D898; font-size: 8px; }
    .hero-label { font-size: 12px; font-weight: 600; color: rgba(255,255,255,0.6); text-transform: uppercase; letter-spacing: .1em; margin-bottom: 8px; }
    .hero-points {
        font-family: 'DM Serif Display', serif;
        font-size: 56px; line-height: 1; margin-bottom: 4px;
    }
    .hero-points span { font-size: 22px; font-weight: 600; color: rgba(255,255,255,0.6); margin-left: 8px; }
    .hero-desc { font-size: 13px; color: rgba(255,255,255,0.65); line-height: 1.6; max-width: 480px; margin-bottom: 20px; }
    .hero-desc strong { color: rgba(255,255,255,0.9); }
    .hero-actions { display: flex; gap: 10px; }
    .hero-btn {
        padding: 10px 20px; border-radius: 50px;
        font-size: 13px; font-weight: 700; font-family: 'Figtree', sans-serif;
        cursor: pointer; transition: all .15s; border: none; text-decoration: none;
    }
    .hero-btn.primary { background: #fff; color: var(--green-deep); }
    .hero-btn.secondary { background: rgba(255,255,255,0.15); color: #fff; }
    .hero-btn.primary:hover { background: #F5F1E6; }
    .hero-btn.secondary:hover { background: rgba(255,255,255,0.22); }

    /* Transaction table */
    .tx-section { background: var(--cream-card); border-radius: 16px; padding: 24px; border: 1px solid rgba(0,0,0,0.06); }
    .tx-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 4px; }
    .tx-title { font-size: 18px; font-weight: 800; color: var(--text-dark); }
    .tx-sub   { font-size: 12px; color: var(--text-muted); margin-bottom: 18px; }

    .tx-table { width: 100%; border-collapse: collapse; }
    .tx-table th { padding: 10px 12px; text-align: left; font-size: 10px; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: .08em; border-bottom: 2px solid rgba(0,0,0,0.07); }
    .tx-table td { padding: 14px 12px; border-bottom: 1px solid rgba(0,0,0,0.05); font-size: 13px; color: var(--text-dark); }
    .tx-table tbody tr:last-child td { border-bottom: none; }
    .tx-table tbody tr:hover td { background: #EDE8D8; }

    .poin-plus { font-weight: 800; color: var(--green-mid); }
    .poin-minus { font-weight: 800; color: #C0392B; }

    .tx-activity { display: flex; align-items: center; gap: 8px; }
    .tx-act-icon { width: 28px; height: 28px; border-radius: 8px; background: #E8E3D3; display: flex; align-items: center; justify-content: center; font-size: 13px; }
    .tx-scroll-hint { text-align: center; font-size: 11px; color: var(--text-muted); padding-top: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: .08em; }
</style>
@endpush

@section('content')
<div class="page-wrap">

@if($isMember)
{{-- ═══════ MEMBER VIEW ═══════ --}}
<div class="member-wrap">
    {{-- Points hero --}}
    <div class="points-hero">
        <div class="hero-badge">Status Anggota Premium</div>
        <div class="hero-label">Saldo Poin Saat Ini</div>
        <div class="hero-points">
            {{ number_format($membership->total_poin_aktif, 0, ',', '.') }}
            <span>Poin</span>
        </div>
        <div class="hero-desc">
            Loyalitas Anda memberi Anda lebih dari sekadar waktu di lapangan. Gunakan poin
            Anda untuk <strong>penyewaan premium</strong>, <strong>peralatan pro-shop</strong> terbaru, dan sesi pelatihan pribadi.
        </div>
        <div class="hero-actions">
            <a href="{{ route('proshop.index') }}" class="hero-btn primary">Tukar Poin</a>
            <button class="hero-btn secondary" onclick="">Lihat Keuntungan</button>
        </div>
    </div>

    {{-- Transaction history --}}
    <div class="tx-section">
        <div class="tx-header">
            <span class="tx-title">Riwayat Transaksi</span>
        </div>
        <div class="tx-sub">Log mendetail tentang aktivitas poin dan pembelian Anda</div>

        @if($pointHistories->count())
        <div style="overflow-x:auto;">
            <table class="tx-table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Aktivitas</th>
                        <th>Poin</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pointHistories as $ph)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($ph->created_at)->format('d M Y') }}</td>
                        <td>
                            <div class="tx-activity">
                                <div class="tx-act-icon">⭐</div>
                                {{ $ph->keterangan }}
                            </div>
                        </td>
                        <td class="{{ $ph->jumlah_poin >= 0 ? 'poin-plus' : 'poin-minus' }}">
                            {{ $ph->jumlah_poin >= 0 ? '+' : '' }}{{ number_format($ph->jumlah_poin, 0, ',', '.') }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="tx-scroll-hint">Gulir untuk melihat transaksi lainnya</div>
        @else
        <div style="text-align:center;padding:32px;color:var(--text-muted);font-size:13px;">Belum ada riwayat transaksi poin.</div>
        @endif
    </div>
</div>

@else
{{-- ═══════ NON-MEMBER VIEW ═══════ --}}
<div class="nonmember-wrap">
    <div class="nm-eyebrow">Eksklusif Anggota</div>
    <h1 class="nm-title">
        Tingkatkan Permainan Anda<br>
        dengan <em>Keanggotaan Elit</em>
    </h1>
    <p class="nm-sub">
        Bergabunglah dengan lingkaran dalam Bandeja Padel Arena. Buka manfaat premium,
        akses prioritas, dan hadiah yang disesuaikan untuk atlet yang berdedikasi.
    </p>

    <div class="pricing-cards">
        {{-- Card kiri --}}
        <div class="price-card">
            <span class="card-icon-lg">🛍</span>
            <div class="card-tag">Manfaat Toko</div>
            <div class="card-name">Cashback Poin<br>5% di Toko</div>
            <div class="card-tagline">
                Dapatkan hadiah pada setiap pembelian peralatan, dari raket kelas profesional hingga bola baru dan aksesori.
            </div>
            <div class="card-spacer"></div>
        </div>

        {{-- Card tengah (featured) --}}
        <div class="price-card featured">
            <span class="card-icon-lg">⭐</span>
            <div class="card-tag">Elite Pass</div>
            <div class="card-name">The Elite Pass</div>
            <div class="card-tagline">Akses penuh ke gaya hidup arena</div>
            <div class="card-price">Rp 100.000</div>
            <div class="card-price-note">Pembelian Sekali / Lifetime</div>
            <div class="card-feature">
                <span class="card-feature-icon">✓</span>
                Prioritas booking lapangan 48 jam sebelumnya
            </div>
            <div class="card-feature">
                <span class="card-feature-icon">✓</span>
                Cashback poin setiap reservasi
            </div>
            <div class="card-feature">
                <span class="card-feature-icon">✓</span>
                Akses promo eksklusif anggota
            </div>
            <div class="card-spacer"></div>
            <a href="#" class="join-btn" id="joinMembershipBtn">Menjadi Anggota</a>
        </div>

        {{-- Card kanan --}}
        <div class="price-card">
            <span class="card-icon-lg">🎾</span>
            <div class="card-tag">Manfaat Sewa</div>
            <div class="card-name">Cashback Poin<br>8% untuk Sewa</div>
            <div class="card-tagline">
                Pengembalian signifikan untuk waktu lapangan, sewa raket, dan penggunaan mesin. Setiap jam bermain membawa Anda lebih dekat ke pertandingan berikutnya.
            </div>
            <div class="card-spacer"></div>
        </div>
    </div>
</div>
@endif

</div>

@if(!$isMember)
<script src="{{ config('midtrans.snap_url') }}" data-client-key="{{ config('midtrans.client_key') }}"></script>
<script>
document.getElementById('joinMembershipBtn')?.addEventListener('click', function (e) {
    e.preventDefault();

    fetch('{{ route("membership.snap-token") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        body: JSON.stringify({})
    })
    .then(response => response.json())
    .then(data => {
        if (!data.snap_token) {
            alert('Gagal memulai pembayaran membership: ' + (data.error || 'Unknown error'));
            return;
        }

        snap.pay(data.snap_token, {
            onSuccess: function(result) {
                const orderId = encodeURIComponent(result?.order_id || '');
                window.location.href = `{{ route('membership.complete') }}?order_id=${orderId}`;
            },
            onPending: function() {
                alert('Pembayaran membership Anda sedang menunggu konfirmasi.');
            },
            onError: function() {
                alert('Pembayaran membership gagal. Silakan coba lagi.');
            },
            onClose: function() {
                // no-op
            }
        });
    })
    .catch(() => {
        alert('Terjadi kesalahan saat memproses pembayaran membership.');
    });
});
</script>
@endif
@endsection
