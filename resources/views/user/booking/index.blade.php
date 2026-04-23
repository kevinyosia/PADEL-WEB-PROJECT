@extends('layouts.user')
@section('title', 'Booking — Bandeja Padel Arena')

@push('styles')
<style>
    :root {
        --green-deep:  #2D4A1E;
        --green-mid:   #3A5C28;
        --cream-bg:    #EDE8D8;
        --cream-card:  #F5F1E6;
        --cream-white: #FAFAF5;
        --text-dark:   #1A1A0F;
        --text-muted:  #6B6B5A;
    }

    .booking-wrap {
        min-height: 100vh; background: var(--cream-bg);
        padding: 28px 32px;
    }
    .booking-back {
        display: inline-flex; align-items: center; gap: 6px;
        font-size: 13px; font-weight: 600; color: var(--text-muted);
        text-decoration: none; margin-bottom: 20px;
        transition: color .15s;
    }
    .booking-back:hover { color: var(--text-dark); }

    .booking-title {
        font-size: 20px; font-weight: 800; color: var(--text-dark);
        margin-bottom: 20px;
    }

    /* Booking summary card */
    .summary-card {
        background: var(--cream-card); border-radius: 16px;
        padding: 20px 22px; margin-bottom: 20px;
        border: 1px solid rgba(0,0,0,0.06);
        display: flex; align-items: center; gap: 18px;
    }
    .summary-court-img {
        width: 80px; height: 60px; border-radius: 10px;
        background: var(--green-mid);
        display: flex; align-items: center; justify-content: center;
        font-size: 28px; flex-shrink: 0;
    }
    .summary-label { font-size: 10px; font-weight: 700; color: #4A7035; text-transform: uppercase; letter-spacing:.08em; margin-bottom: 4px; }
    .summary-name  { font-size: 18px; font-weight: 800; color: var(--text-dark); margin-bottom: 8px; font-family: 'DM Serif Display', serif; }
    .summary-meta  { display: flex; flex-wrap: wrap; gap: 14px; }
    .meta-item     { display: flex; align-items: center; gap: 5px; font-size: 12px; color: var(--text-muted); font-weight: 600; }

    /* Accordion sections */
    .addon-section { margin-bottom: 10px; }
    .accordion-header {
        background: var(--cream-card); border-radius: 14px;
        padding: 16px 20px;
        display: flex; align-items: center; justify-content: space-between;
        cursor: pointer; transition: background .15s;
        border: 1px solid rgba(0,0,0,0.06);
    }
    .accordion-header:hover { background: #EDE8D8; }
    .accordion-header.open { border-radius: 14px 14px 0 0; border-bottom: none; }
    .acc-left { display: flex; align-items: center; gap: 12px; }
    .acc-icon {
        width: 36px; height: 36px; border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 17px;
    }
    .acc-icon.green { background: var(--green-mid); }
    .acc-icon.cream { background: #E8E3D3; }
    .acc-title { font-size: 15px; font-weight: 700; color: var(--text-dark); }
    .acc-chevron { font-size: 14px; color: var(--text-muted); transition: transform .2s; }
    .acc-chevron.open { transform: rotate(180deg); }

    .accordion-body {
        display: none; background: var(--cream-card);
        border: 1px solid rgba(0,0,0,0.06); border-top: none;
        border-radius: 0 0 14px 14px; overflow: hidden;
    }
    .accordion-body.open { display: block; }

    /* Coach option */
    .coach-option {
        display: flex; align-items: center; gap: 14px;
        padding: 14px 20px; cursor: pointer;
        border-bottom: 1px solid rgba(0,0,0,0.05);
        transition: background .15s;
    }
    .coach-option:hover { background: #EDE8D8; }
    .coach-option:last-child { border-bottom: none; }
    .coach-option.selected { background: #E6EDD8; }
    .coach-avatar {
        width: 40px; height: 40px; border-radius: 50%;
        background: #C5C0B0; flex-shrink: 0;
        display: flex; align-items: center; justify-content: center;
        font-size: 16px; font-weight: 800; color: #fff;
    }
    .coach-name  { font-size: 14px; font-weight: 700; color: var(--text-dark); }
    .coach-desc  { font-size: 11px; color: var(--text-muted); margin-top: 1px; }
    .coach-price { font-size: 13px; font-weight: 700; color: var(--green-deep); margin-left: auto; flex-shrink: 0; }
    .coach-check {
        width: 22px; height: 22px; border-radius: 6px;
        border: 2px solid #C5C0B0; flex-shrink: 0;
        display: flex; align-items: center; justify-content: center;
        transition: all .15s;
    }
    .coach-check.checked { background: var(--green-mid); border-color: var(--green-mid); }
    .coach-check.checked::after { content: '✓'; color: #fff; font-size: 12px; font-weight: 800; }

    /* Equipment row */
    .equip-row {
        display: flex; align-items: center; gap: 12px;
        padding: 13px 20px;
        border-bottom: 1px solid rgba(0,0,0,0.05);
    }
    .equip-row:last-child { border-bottom: none; }
    .equip-icon {
        width: 34px; height: 34px; border-radius: 8px;
        background: #E8E3D3; display: flex; align-items: center;
        justify-content: center; font-size: 15px; flex-shrink: 0;
    }
    .equip-name  { font-size: 14px; font-weight: 700; color: var(--text-dark); }
    .equip-price { font-size: 12px; color: var(--text-muted); margin-top: 1px; }
    .equip-price strong { color: var(--green-deep); }
    .qty-wrap {
        display: flex; align-items: center; gap: 8px; margin-left: auto; flex-shrink: 0;
    }
    .qty-btn {
        width: 28px; height: 28px; border-radius: 50%;
        background: #E8E3D3; border: none; cursor: pointer;
        font-size: 16px; font-weight: 700; color: var(--text-dark);
        display: flex; align-items: center; justify-content: center;
        transition: background .15s;
    }
    .qty-btn:hover { background: #DDD8C8; }
    .qty-val { font-size: 14px; font-weight: 800; color: var(--text-dark); min-width: 20px; text-align: center; }

    /* Bottom bar */
    .bottom-bar {
        position: sticky; bottom: 0;
        background: var(--cream-white);
        border-top: 1px solid rgba(0,0,0,0.08);
        padding: 16px 32px;
        display: flex; align-items: center; justify-content: space-between;
        margin: 0 -32px;
    }
    .total-label { font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing:.07em; }
    .total-price { font-size: 22px; font-weight: 800; color: var(--text-dark); }
    .continue-btn {
        padding: 13px 32px; background: var(--green-deep); color: #fff;
        border: none; border-radius: 50px;
        font-size: 15px; font-weight: 700; font-family: 'Figtree', sans-serif;
        cursor: pointer; transition: all .18s;
        display: flex; align-items: center; gap: 8px;
    }
    .continue-btn:hover { background: var(--green-mid); }

    /* Confirm Modal */
    .modal-overlay {
        display: none; position: fixed; inset: 0;
        background: rgba(0,0,0,0.5); z-index: 9000;
        align-items: center; justify-content: center;
    }
    .modal-overlay.show { display: flex; }
    .modal-box {
        background: #fff; border-radius: 20px; padding: 36px 32px;
        width: 420px; text-align: center;
        box-shadow: 0 20px 60px rgba(0,0,0,0.2);
    }
    .modal-icon  { font-size: 44px; margin-bottom: 12px; }
    .modal-title { font-size: 22px; font-weight: 800; color: var(--text-dark); margin-bottom: 6px; }
    .modal-sub   { font-size: 13px; color: var(--text-muted); margin-bottom: 22px; }
    .modal-breakdown { text-align: left; border-top: 1px solid #eee; padding-top: 16px; margin-bottom: 22px; }
    .modal-line { display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 13px; color: var(--text-muted); }
    .modal-line.total { font-weight: 800; font-size: 15px; color: var(--text-dark); border-top: 1px solid #eee; padding-top: 10px; margin-top: 4px; }
    .modal-actions { display: flex; gap: 10px; }
    .btn-back-modal {
        flex: 1; padding: 12px; background: #F5F1E6; color: var(--text-muted);
        border: 1px solid #E8E3D3; border-radius: 50px;
        font-size: 14px; font-weight: 700; font-family: 'Figtree', sans-serif;
        cursor: pointer;
    }
    .btn-confirm {
        flex: 1; padding: 12px; background: var(--green-deep); color: #fff;
        border: none; border-radius: 50px;
        font-size: 14px; font-weight: 700; font-family: 'Figtree', sans-serif;
        cursor: pointer; transition: background .15s;
    }
    .btn-confirm:hover { background: var(--green-mid); }

    /* Payment channel */
    .payment-section { margin-bottom: 20px; }
    .payment-title { font-size: 13px; font-weight: 700; color: var(--text-muted); margin-bottom: 10px; text-transform: uppercase; letter-spacing:.07em; }
    .payment-options { display: flex; gap: 10px; }
    .payment-opt {
        flex: 1; padding: 12px 16px;
        border: 2px solid #E8E3D3; border-radius: 12px;
        cursor: pointer; transition: all .15s; text-align: center;
        font-size: 13px; font-weight: 700; color: var(--text-muted);
        background: var(--cream-card);
    }
    .payment-opt.selected { border-color: var(--green-mid); color: var(--green-deep); background: #E6EDD8; }
</style>
@endpush

@section('content')
<div class="booking-wrap">
    <a href="{{ route('courts.index') }}" class="booking-back">← Kembali ke Pilih Lapangan</a>
        <div class="booking-title">Pro Shops</div>
    <div class="summary-card">
        <div class="summary-court-img">🏸</div>
        <div>
            <div class="summary-label">Court Booking</div>
            <div class="summary-name">{{ $courtName ?? 'Bandeja Padel Arena' }}</div>
            <div class="summary-meta">
                <span class="meta-item">📅 {{ $tanggalFormatted ?? request('tanggal_booking') }}</span>
                <span class="meta-item">⏰ {{ request('jam_mulai','—') }} – {{ request('jam_selesai','—') }}
                    ({{ $durasiJam ?? 1 }} Jam)</span>
                <span class="meta-item">🏟 {{ $courtName ?? '—' }}</span>
            </div>
        </div>
        <div style="margin-left:auto;text-align:right;">
            <div style="font-size:11px;color:var(--text-muted);font-weight:600;">Sewa Lapangan</div>
            <div style="font-size:18px;font-weight:800;color:var(--green-deep);">Rp{{ number_format($courtPrice ?? 0,0,',','.') }}</div>
        </div>
    </div>

    {{-- ── COACH accordion ── --}}
    <div class="addon-section">
        <div class="accordion-header" id="accCoachHeader" onclick="toggleAcc('coach')">
            <div class="acc-left">
                <div class="acc-icon green">👤</div>
                <div class="acc-title">Coach</div>
            </div>
            <span class="acc-chevron" id="accCoachChevron">▼</span>
        </div>
        <div class="accordion-body" id="accCoachBody">
            <div class="coach-option" style="cursor:default;background:#F8F5EE;">
                <div style="font-size:12px;color:var(--text-muted);padding:2px 0;">Pilih coach (opsional)</div>
            </div>
            @forelse($coaches as $coach)
            <div class="coach-option" id="coachOpt{{ $coach->id }}" onclick="selectCoach({{ $coach->id }}, {{ $coach->harga_per_jam }})">
                <div class="coach-avatar">{{ strtoupper(substr($coach->user->name ?? 'C', 0, 1)) }}</div>
                <div>
                    <div class="coach-name">{{ $coach->user->name ?? 'Coach' }}</div>
                    <div class="coach-desc">{{ Str::limit($coach->deskripsi_keahlian, 50) }}</div>
                </div>
                <div class="coach-price">Rp{{ number_format($coach->harga_per_jam,0,',','.') }}/jam</div>
                <div class="coach-check" id="coachCheck{{ $coach->id }}"></div>
            </div>
            @empty
            <div style="padding:20px;text-align:center;color:var(--text-muted);font-size:13px;">Tidak ada coach tersedia.</div>
            @endforelse
        </div>
    </div>

    {{-- ── EQUIPMENT accordion ── --}}
    <div class="addon-section">
        <div class="accordion-header" id="accEquipHeader" onclick="toggleAcc('equip')">
            <div class="acc-left">
                <div class="acc-icon cream">🎾</div>
                <div class="acc-title">Rental Perlengkapan</div>
            </div>
            <span class="acc-chevron" id="accEquipChevron">▼</span>
        </div>
        <div class="accordion-body" id="accEquipBody">
            @forelse($equipments->where('kategori','sewa') as $eq)
            <div class="equip-row">
                <div class="equip-icon">🎾</div>
                <div>
                    <div class="equip-name">{{ $eq->nama_alat }}</div>
                    <div class="equip-price"><strong>Rp{{ number_format($eq->harga,0,',','.') }}</strong>/item</div>
                </div>
                <div class="qty-wrap">
                    <button class="qty-btn" onclick="changeQty({{ $eq->id }}, {{ $eq->harga }}, -1)">−</button>
                    <span class="qty-val" id="qty{{ $eq->id }}">0</span>
                    <button class="qty-btn" onclick="changeQty({{ $eq->id }}, {{ $eq->harga }}, 1)">+</button>
                </div>
            </div>
            @empty
            <div style="padding:20px;text-align:center;color:var(--text-muted);font-size:13px;">Tidak ada perlengkapan tersedia.</div>
            @endforelse
        </div>
    </div>

    {{-- ── PRODUCTS accordion ── --}}
    <div class="addon-section">
        <div class="accordion-header" id="accProductHeader" onclick="toggleAcc('product')">
            <div class="acc-left">
                <div class="acc-icon cream">⚽</div>
                <div class="acc-title">Pro Shops</div>
            </div>
            <span class="acc-chevron" id="accProductChevron">▼</span>
        </div>
        <div class="accordion-body" id="accProductBody">
            @forelse($products as $prod)
            <div class="equip-row">
                <div class="equip-icon">⚽</div>
                <div>
                    <div class="equip-name">{{ $prod->nama_alat }}</div>
                    <div class="equip-price"><strong>Rp{{ number_format($prod->harga,0,',','.') }}</strong>/item</div>
                </div>
                <div class="qty-wrap">
                    <button class="qty-btn" onclick="changeProductQty({{ $prod->id }}, {{ $prod->harga }}, -1)">−</button>
                    <span class="qty-val" id="prod{{ $prod->id }}">0</span>
                    <button class="qty-btn" onclick="changeProductQty({{ $prod->id }}, {{ $prod->harga }}, 1)">+</button>
                </div>
            </div>
            @empty
            <div style="padding:20px;text-align:center;color:var(--text-muted);font-size:13px;">Tidak ada produk tersedia.</div>
            @endforelse
        </div>
    </div>

    {{-- Payment Channel --}}
    <div class="payment-section" style="margin-top:20px;">
        <div class="payment-title">Metode Pembayaran</div>
        <div class="payment-options">
            <div class="payment-opt selected" id="payVA" onclick="selectPayment('virtual_account')">Virtual Account</div>
            <div class="payment-opt" id="payMB" onclick="selectPayment('m_banking')">M-Banking</div>
        </div>
    </div>

    {{-- Spacer for sticky bar --}}
    <div style="height:80px;"></div>

    {{-- Bottom bar --}}
    <div class="bottom-bar">
        <div>
            <div class="total-label">Total Bayar</div>
            <div class="total-price" id="totalDisplay">Rp{{ number_format($courtPrice ?? 0,0,',','.') }}</div>
        </div>
        <button class="continue-btn" onclick="openConfirm()">Selanjutnya →</button>
    </div>
</div>

{{-- Confirm Modal --}}
<div class="modal-overlay" id="confirmModal">
    <div class="modal-box">
        <div class="modal-icon">✅</div>
        <div class="modal-title">Konfirmasi Pesanan?</div>
        <div class="modal-sub">Satu langkah lagi untuk mengamankan slot Arena Padel Anda.</div>
        <div class="modal-breakdown">
            <div class="modal-line"><span>Sewa Lapangan</span><span id="mCourtPrice">Rp{{ number_format($courtPrice ?? 0,0,',','.') }}</span></div>
            <div class="modal-line" id="mCoachLine" style="display:none;"><span>Coach</span><span id="mCoachPrice">—</span></div>
            <div class="modal-line" id="mEquipLine" style="display:none;"><span>Perlengkapan</span><span id="mEquipPrice">—</span></div>
            <div class="modal-line" id="mProductLine" style="display:none;"><span>Pro Shops</span><span id="mProductPrice">—</span></div>
            <div class="modal-line total"><span>Total Bayar</span><span id="mTotal">Rp{{ number_format($courtPrice ?? 0,0,',','.') }}</span></div>
        </div>
        <div class="modal-actions">
            <button class="btn-back-modal" onclick="closeConfirm()">← Cek Ulang</button>
            <button class="btn-confirm" onclick="submitBooking()">Lanjut →</button>
        </div>
    </div>
</div>

{{-- Hidden form --}}
<form id="finalForm" method="POST" action="{{ route('booking.store') }}" style="display:none;">
    @csrf
    <input type="hidden" name="court_id"        value="{{ request('court_id') }}">
    <input type="hidden" name="tanggal_booking" value="{{ request('tanggal_booking') }}">
    <input type="hidden" name="jam_mulai"       value="{{ request('jam_mulai') }}">
    <input type="hidden" name="jam_selesai"     value="{{ request('jam_selesai') }}">
    <input type="hidden" name="coach_id"        id="finalCoachId" value="">
    <input type="hidden" name="payment_channel" id="finalPayment" value="virtual_account">
    <div id="equipInputs"></div>
</form>
@endsection

@push('scripts')
<script>
const COURT_PRICE  = {{ $courtPrice ?? 0 }};
const DURASI_JAM   = {{ $durasiJam ?? 1 }};
let selectedCoach  = null;
let coachPrice     = 0;
let equipmentQtys  = {};   // { id: { qty, price } }
let productQtys    = {};   // { id: { qty, price } }
let paymentChannel = 'virtual_account';

function fmtRp(n) { return Number(n).toLocaleString('id-ID'); }

// ── Accordion ──
function toggleAcc(key) {
    let section = 'Coach';
    if (key === 'equip') section = 'Equip';
    else if (key === 'product') section = 'Product';
    const body    = document.getElementById(`acc${section}Body`);
    const header  = document.getElementById(`acc${section}Header`);
    const chevron = document.getElementById(`acc${section}Chevron`);
    const isOpen  = body.classList.contains('open');
    body.classList.toggle('open', !isOpen);
    header.classList.toggle('open', !isOpen);
    chevron.classList.toggle('open', !isOpen);
}

// ── Coach ──
function selectCoach(id, price) {
    if (selectedCoach === id) {
        // deselect
        document.getElementById(`coachOpt${id}`).classList.remove('selected');
        document.getElementById(`coachCheck${id}`).classList.remove('checked');
        selectedCoach = null; coachPrice = 0;
    } else {
        if (selectedCoach) {
            document.getElementById(`coachOpt${selectedCoach}`)?.classList.remove('selected');
            document.getElementById(`coachCheck${selectedCoach}`)?.classList.remove('checked');
        }
        document.getElementById(`coachOpt${id}`).classList.add('selected');
        document.getElementById(`coachCheck${id}`).classList.add('checked');
        selectedCoach = id;
        coachPrice = price * DURASI_JAM;
    }
    updateTotal();
}

// ── Equipment ──
function changeQty(id, price, delta) {
    if (!equipmentQtys[id]) equipmentQtys[id] = { qty: 0, price };
    equipmentQtys[id].qty = Math.max(0, equipmentQtys[id].qty + delta);
    document.getElementById(`qty${id}`).textContent = equipmentQtys[id].qty;
    updateTotal();
}

// ── Products ──
function changeProductQty(id, price, delta) {
    if (!productQtys[id]) productQtys[id] = { qty: 0, price };
    productQtys[id].qty = Math.max(0, productQtys[id].qty + delta);
    document.getElementById(`prod${id}`).textContent = productQtys[id].qty;
    updateTotal();
}

// ── Payment ──
function selectPayment(val) {
    paymentChannel = val;
    document.getElementById('payVA').classList.toggle('selected', val==='virtual_account');
    document.getElementById('payMB').classList.toggle('selected', val==='m_banking');
}

// ── Total ──
function equipTotal() {
    return Object.values(equipmentQtys).reduce((s,e) => s + (e.qty * e.price), 0);
}
function productTotal() {
    return Object.values(productQtys).reduce((s,e) => s + (e.qty * e.price), 0);
}
function grandTotal() { return COURT_PRICE + coachPrice + equipTotal() + productTotal(); }

function updateTotal() {
    document.getElementById('totalDisplay').textContent = 'Rp' + fmtRp(grandTotal());
}

// ── Confirm modal ──
function openConfirm() {
    const et = equipTotal();
    const pt = productTotal();
    document.getElementById('mCourtPrice').textContent = 'Rp' + fmtRp(COURT_PRICE);
    if (coachPrice > 0) {
        document.getElementById('mCoachLine').style.display = '';
        document.getElementById('mCoachPrice').textContent  = 'Rp' + fmtRp(coachPrice);
    } else {
        document.getElementById('mCoachLine').style.display = 'none';
    }
    if (et > 0) {
        document.getElementById('mEquipLine').style.display = '';
        document.getElementById('mEquipPrice').textContent  = 'Rp' + fmtRp(et);
    } else {
        document.getElementById('mEquipLine').style.display = 'none';
    }
    if (pt > 0) {
        document.getElementById('mProductLine').style.display = '';
        document.getElementById('mProductPrice').textContent  = 'Rp' + fmtRp(pt);
    } else {
        document.getElementById('mProductLine').style.display = 'none';
    }
    document.getElementById('mTotal').textContent = 'Rp' + fmtRp(grandTotal());
    document.getElementById('confirmModal').classList.add('show');
}
function closeConfirm() { document.getElementById('confirmModal').classList.remove('show'); }

// ── Submit ──
function submitBooking() {
    document.getElementById('finalCoachId').value = selectedCoach ?? '';
    document.getElementById('finalPayment').value = paymentChannel;

    const equipWrap = document.getElementById('equipInputs');
    equipWrap.innerHTML = '';
    let idx = 0;
    Object.entries(equipmentQtys).forEach(([id, {qty}]) => {
        if (qty > 0) {
            equipWrap.innerHTML += `<input type="hidden" name="equipment_items[${idx}][equipment_id]" value="${id}">`;
            equipWrap.innerHTML += `<input type="hidden" name="equipment_items[${idx}][jumlah]" value="${qty}">`;
            idx++;
        }
    });
    
    let pidx = 0;
    Object.entries(productQtys).forEach(([id, {qty}]) => {
        if (qty > 0) {
            equipWrap.innerHTML += `<input type="hidden" name="product_items[${pidx}][product_id]" value="${id}">`;
            equipWrap.innerHTML += `<input type="hidden" name="product_items[${pidx}][jumlah]" value="${qty}">`;
            pidx++;
        }
    });
    document.getElementById('finalForm').submit();
}
</script>
@endpush
