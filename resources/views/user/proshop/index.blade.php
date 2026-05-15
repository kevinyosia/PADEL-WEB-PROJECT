@extends('layouts.user')
@section('title', 'Pro Shop — Bandeja Padel Arena')

@push('styles')
<style>
    :root {
        --green-deep: #2D4A1E; --green-mid: #3A5C28;
        --cream-bg: #EDE8D8; --cream-card: #F5F1E6; --cream-white: #FAFAF5;
        --text-dark: #1A1A0F; --text-muted: #6B6B5A;
    }
    .page-wrap { min-height: 100vh; background: var(--cream-bg); padding: 28px 32px; }

    .shop-header { margin-bottom: 8px; }
    .shop-eyebrow { font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: .1em; margin-bottom: 6px; }
    .shop-title { font-family: 'DM Serif Display', serif; font-size: 32px; color: var(--text-dark); }
    .shop-title em { font-style: italic; color: var(--green-mid); }

    .shop-notice {
        background: #EFF7E8; border: 1px solid #C2DEB0;
        border-radius: 10px; padding: 11px 16px;
        font-size: 12px; color: var(--green-deep);
        display: flex; align-items: center; gap: 8px;
        margin-bottom: 28px;
    }

    /* Section */
    .section-header {
        display: flex; align-items: center; justify-content: space-between;
        margin-bottom: 16px; margin-top: 28px;
    }
    .section-title { font-size: 18px; font-weight: 800; color: var(--text-dark); }
    .section-sub   { font-size: 11px; font-weight: 600; color: var(--text-muted); letter-spacing:.05em; text-transform: uppercase; }

    /* Product grid */
    .product-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; }

    .product-card {
        background: var(--cream-card); border-radius: 16px; overflow: hidden;
        border: 1px solid rgba(0,0,0,0.06); transition: box-shadow .18s;
    }
    .product-card:hover { box-shadow: 0 6px 24px rgba(0,0,0,0.1); }

    .product-img {
        width: 100%; height: 160px; object-fit: cover;
        background: linear-gradient(135deg, #C5C0B0, #D9D4C4);
        display: flex; align-items: center; justify-content: center;
        font-size: 48px;
    }
    .product-body { padding: 14px 16px; }

    .product-badge {
        display: inline-flex; align-items: center; gap: 4px;
        font-size: 9px; font-weight: 800; padding: 3px 8px;
        border-radius: 20px; text-transform: uppercase; letter-spacing: .06em;
        margin-bottom: 8px;
    }
    .badge-in-stock   { background: #D6EDCC; color: #2D5016; }
    .badge-low-stock  { background: #FEF3CD; color: #92610A; }
    .badge-out-stock  { background: #FCE0DC; color: #8B2020; }

    .product-name  { font-size: 15px; font-weight: 800; color: var(--text-dark); margin-bottom: 4px; }
    .product-desc  { font-size: 11px; color: var(--text-muted); line-height: 1.5; margin-bottom: 12px;
        display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }

    .product-footer { display: flex; align-items: center; justify-content: space-between; }
    .product-price { font-size: 16px; font-weight: 800; color: var(--text-dark); }
    .price-unit    { font-size: 11px; color: var(--text-muted); font-weight: 600; }

    .buy-tag {
        font-size: 10px; font-weight: 700; color: var(--text-muted);
        background: #E8E3D3; padding: 4px 10px; border-radius: 20px;
    }

    .empty-section { text-align: center; padding: 40px; color: var(--text-muted); font-size: 13px; }

    /* Category tabs */
    .cat-tabs { display: flex; gap: 8px; margin-bottom: 0; flex-wrap: wrap; }
    .cat-tab {
        padding: 7px 16px; border-radius: 50px;
        background: var(--cream-card); border: 1.5px solid rgba(0,0,0,0.08);
        font-size: 12px; font-weight: 700; color: var(--text-muted);
        cursor: pointer; transition: all .15s;
    }
    .cat-tab.active { background: var(--green-deep); color: #fff; border-color: var(--green-deep); }
</style>
@endpush

@section('content')
<div class="page-wrap">
    <div class="shop-header">
        <div class="shop-eyebrow">Premium Gear</div>
        <h1 class="shop-title">Pro Shop — <em>Padel Equipment</em></h1>
    </div>

    <div class="shop-notice">
        <span>ℹ️</span>
        Items are available for purchase directly at the arena during your scheduled court booking.
    </div>

    {{-- Group by category --}}
    @php
        $grouped = $equipments->groupBy('nama_alat');
        $categories = $equipments->pluck('nama_alat')->unique()->values();
        // For display we'll use a simpler grouping: just show all items
    @endphp

    <div class="section-header">
        <span class="section-title">Padel Equipment</span>
        <span class="section-sub">For Purchase</span>
    </div>

    @if($equipments->count())
    <div class="product-grid">
        @foreach($equipments as $eq)
        @php
            $stockStatus = $eq->stock_status;
            $stockLabel  = $stockStatus === 'sold_out' ? 'Sold Out' : 'In Stock';
            $stockClass  = $stockStatus === 'sold_out' ? 'badge-out-stock' : 'badge-in-stock';
            $isDisabled  = $stockStatus === 'sold_out';
        @endphp
        <div class="product-card" @if($isDisabled) style="opacity: 0.6;" @endif>
            <div class="product-img">🎾</div>
            <div class="product-body">
                <span class="product-badge {{ $stockClass }}">● {{ $stockLabel }}</span>
                <div class="product-name">{{ $eq->nama_alat }}</div>
                @if($eq->deskripsi)
                    <div class="product-desc">{{ $eq->deskripsi }}</div>
                @else
                    <div class="product-desc">Available for purchase at the arena.</div>
                @endif
                <div class="product-footer">
                    <div>
                        <div class="product-price">{{ number_format($eq->harga,0,',','.') }} IDR</div>
                    </div>
                    @if(!$isDisabled)
                        <span class="buy-tag">Beli</span>
                    @else
                        <span class="buy-tag" style="background: #FCE0DC; color: #8B2020;">Habis</span>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="empty-section">
        <div style="font-size:36px;margin-bottom:10px;">🛍</div>
        <p>Belum ada produk yang tersedia.</p>
    </div>
    @endif
</div>
@endsection
