@extends('layouts.user')
@section('title', 'Courts — Bandeja Padel Arena')

@push('styles')
<style>
    :root {
        --green-deep:  #2D4A1E;
        --green-mid:   #3A5C28;
        --green-btn:   #3D5C2A;
        --cream-bg:    #EDE8D8;
        --cream-card:  #F5F1E6;
        --cream-white: #FAFAF5;
        --text-dark:   #1A1A0F;
        --text-muted:  #6B6B5A;
        --gold:        #C8922A;
    }

    .page-wrap {
        min-height: 100vh;
        background: var(--cream-bg);
        padding: 0;
        display: flex; flex-direction: column;
    }

    /* ── Topbar ── */
    .page-topbar {
        background: var(--cream-card);
        border-bottom: 1px solid rgba(0,0,0,0.07);
        padding: 14px 32px;
        display: flex; align-items: center; justify-content: space-between;
    }
    .page-topbar h1 {
        font-family: 'DM Serif Display', serif;
        font-size: 22px; color: var(--text-dark);
    }

    /* ── ADS Banner ── */
    .ads-banner {
        margin: 20px 28px 0;
        background: #D9D4C4;
        border-radius: 16px;
        height: 110px;
        display: flex; align-items: center; justify-content: center;
        color: var(--text-muted); font-size: 13px; font-weight: 600;
        letter-spacing: 0.08em; text-transform: uppercase;
        border: 1px dashed rgba(0,0,0,0.12);
    }

    /* ── Body layout ── */
    .body-layout {
        display: flex; gap: 0;
        flex: 1; margin-top: 20px;
        padding: 0 28px 28px;
        gap: 20px;
    }

    /* ── Left: floor plan + choose courts ── */
    .left-col { flex: 1; min-width: 0; }

    /* Floor plan illustration */
    .floor-plan-wrap {
        background: var(--cream-card);
        border-radius: 16px;
        padding: 20px;
        margin-bottom: 16px;
        border: 1px solid rgba(0,0,0,0.06);
    }
    .floor-plan-svg {
        width: 100%;
        height: auto;
        display: block;
        border-radius: 8px;
    }
    .court-tile {
        display: none;
    }

    /* Choose courts panel */
    .choose-panel {
        background: var(--cream-card);
        border-radius: 16px; padding: 18px 20px;
        border: 1px solid rgba(0,0,0,0.06);
    }
    .choose-title {
        font-size: 11px; font-weight: 800; color: var(--text-muted);
        text-transform: uppercase; letter-spacing: 0.1em;
        margin-bottom: 14px;
    }

    /* Date row */
    .date-row {
        display: flex; align-items: center; gap: 10px;
        margin-bottom: 14px;
    }
    .date-display {
        display: flex; flex-direction: column;
        background: #E8E3D3; border-radius: 10px;
        padding: 8px 14px; cursor: pointer;
        transition: background 0.15s;
        border: 1.5px solid transparent;
    }
    .date-display:hover { background: #DDD8C8; border-color: var(--green-mid); }
    .date-day  { font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; }
    .date-num  { font-size: 20px; font-weight: 800; color: var(--text-dark); line-height: 1.1; }
    .date-mon  { font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; }
    .date-sep  { color: #C5C0B0; font-size: 18px; }
    .cal-btn {
        width: 42px; height: 42px; border-radius: 10px;
        background: #E8E3D3; border: none; cursor: pointer;
        display: flex; align-items: center; justify-content: center;
        font-size: 18px; transition: background 0.15s; margin-left: auto;
    }
    .cal-btn:hover { background: #DDD8C8; }

    /* Calendar popup */
    .cal-popup {
        display: none; position: absolute;
        background: var(--cream-white); border-radius: 14px;
        padding: 16px; box-shadow: 0 12px 40px rgba(0,0,0,0.15);
        z-index: 500; min-width: 560px;
        border: 1px solid rgba(0,0,0,0.08);
    }
    .cal-popup.show { display: block; }
    .cal-header {
        display: flex; align-items: center; justify-content: space-between;
        margin-bottom: 10px;
    }
    .cal-nav { background: none; border: none; cursor: pointer; font-size: 16px; color: var(--text-muted); padding: 4px 8px; }
    .cal-nav:hover { color: var(--text-dark); }
    .cal-months { display: flex; gap: 24px; }
    .cal-month { flex: 1; }
    .cal-month-title { font-size: 13px; font-weight: 700; color: var(--text-dark); text-align: center; margin-bottom: 8px; }
    .cal-grid { display: grid; grid-template-columns: repeat(7, 1fr); gap: 2px; }
    .cal-dow { font-size: 9px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; text-align: center; padding: 3px 0; }
    .cal-day {
        text-align: center; padding: 5px 2px; border-radius: 6px;
        font-size: 11px; font-weight: 600; cursor: pointer;
        color: var(--text-dark); transition: all 0.12s;
    }
    .cal-day:hover:not(.empty):not(.past) { background: #D9D4C4; }
    .cal-day.today { background: var(--green-mid); color: #fff; }
    .cal-day.selected { background: var(--green-deep); color: #fff; }
    .cal-day.past { color: #C5C0B0; cursor: not-allowed; }
    .cal-day.empty { cursor: default; }

    /* Court list */
    .court-list { display: flex; flex-direction: column; gap: 8px; margin-top: 8px; }
    .court-row {
        display: flex; align-items: center; justify-content: space-between;
        background: #E8E3D3; border-radius: 12px; padding: 12px 16px;
        cursor: pointer; transition: all 0.18s;
        border: 1.5px solid transparent;
    }
    .court-row:hover { background: #DDD8C8; }
    .court-row.active { background: var(--cream-white); border-color: var(--green-mid); }
    .court-row-name { font-size: 13px; font-weight: 700; color: var(--text-dark); }
    .court-row-status {
        font-size: 10px; font-weight: 700; padding: 3px 8px;
        border-radius: 20px; text-transform: uppercase; letter-spacing: 0.05em;
    }
    .status-available { background: #D6EDCC; color: #2D5016; }
    .status-maintenance { background: #E5E0D0; color: #7A7A6A; }

    /* ── Right col: slots ── */
    .right-col {
        width: 320px; flex-shrink: 0;
        display: flex; flex-direction: column; gap: 14px;
    }

    .slot-panel {
        background: var(--cream-card); border-radius: 16px;
        padding: 18px 20px;
        border: 1px solid rgba(0,0,0,0.06);
        flex: 1; overflow: hidden;
    }
    .slot-panel-title {
        font-family: 'DM Serif Display', serif;
        font-size: 16px; color: var(--text-dark); margin-bottom: 14px;
    }
    .slot-empty {
        text-align: center; padding: 40px 20px;
        color: var(--text-muted); font-size: 13px;
    }

    /* Slot grid */
    .slot-grid {
        display: grid; grid-template-columns: repeat(4, 1fr);
        gap: 6px; max-height: 420px; overflow-y: auto;
        padding-right: 2px;
    }
    .slot-grid::-webkit-scrollbar { width: 4px; }
    .slot-grid::-webkit-scrollbar-thumb { background: #C5C0B0; border-radius: 4px; }

    .slot-cell {
        border-radius: 8px; padding: 6px 4px;
        text-align: center; font-size: 10px; font-weight: 600;
        cursor: pointer; transition: all 0.15s;
        border: 1.5px solid transparent;
        position: relative;
    }
    .slot-cell.available {
        background: #D6EDCC; color: #2D5016;
    }
    .slot-cell.available:hover {
        background: #C2E5B4; border-color: var(--green-mid);
        transform: scale(1.04);
    }
    .slot-cell.available.selected {
        background: var(--green-mid); color: #fff;
        border-color: var(--green-deep);
    }
    .slot-cell.booked {
        background: #F0C8C8; color: #8B2020; cursor: not-allowed;
    }
    .slot-cell.maintenance {
        background: #E5E0D0; color: #9A9A8A; cursor: not-allowed;
    }
    .slot-time { font-size: 9px; display: block; }
    .slot-price { font-size: 9px; display: block; margin-top: 2px; opacity: 0.8; }
    .slot-label { font-size: 8px; display: block; margin-top: 1px; font-weight: 700; }

    /* Next button */
    .next-btn {
        width: 100%; padding: 14px;
        background: var(--green-deep); color: #fff;
        border: none; border-radius: 50px;
        font-size: 15px; font-weight: 700; font-family: 'Figtree', sans-serif;
        cursor: pointer; transition: all 0.18s;
        display: flex; align-items: center; justify-content: center; gap: 8px;
    }
    .next-btn:hover { background: var(--green-mid); }
    .next-btn:disabled { background: #C5C0B0; cursor: not-allowed; }

    /* Slot legend */
    .slot-legend {
        display: flex; gap: 10px; flex-wrap: wrap;
        font-size: 10px; font-weight: 600; color: var(--text-muted);
        margin-bottom: 10px;
    }
    .legend-dot {
        width: 10px; height: 10px; border-radius: 3px; display: inline-block; margin-right: 4px;
    }
    .ld-avail { background: #D6EDCC; }
    .ld-booked { background: #F0C8C8; }
    .ld-maint  { background: #E5E0D0; }
    .ld-sel    { background: var(--green-mid); }
</style>
@endpush

@section('content')
<div class="page-wrap">

    {{-- Topbar --}}
    <div class="page-topbar">
        <h1>Bandeja Padel Arena</h1>
        <div style="font-size:13px;color:var(--text-muted);">Ancol, Jakarta Utara</div>
    </div>

    {{-- ADS --}}
    <div class="ads-banner">ADS</div>

    {{-- Body --}}
    <div class="body-layout">

        {{-- LEFT --}}
        <div class="left-col">

            {{-- Floor Plan --}}
            <div class="floor-plan-wrap">
                <img src="{{ asset('images/Denah Lapangan Padel.png') }}" alt="Denah Lapangan Padel" class="floor-plan-svg" style="margin-bottom: 16px;">
                <div id="floorPlan" style="display: none;"></div>
            </div>

            {{-- Choose Courts Panel --}}
            <div class="choose-panel" style="position:relative;">
                <div class="choose-title">Choose Courts</div>

                {{-- Date picker --}}
                <div class="date-row">
                    <div class="date-display" id="dateDisplay" onclick="toggleCal()">
                        <span class="date-day" id="labelDay">—</span>
                        <span class="date-num" id="labelDate">—</span>
                        <span class="date-mon" id="labelMonth">—</span>
                    </div>
                    <span class="date-sep">|</span>
                    <div id="dayNameDisplay" style="font-size:13px;font-weight:600;color:var(--text-muted);">Pilih tanggal</div>
                    <button class="cal-btn" onclick="toggleCal()">📅</button>

                    {{-- Calendar popup --}}
                    <div class="cal-popup" id="calPopup">
                        <div class="cal-header">
                            <button class="cal-nav" id="calPrev">‹</button>
                            <div class="cal-months" id="calMonths"></div>
                            <button class="cal-nav" id="calNext">›</button>
                        </div>
                    </div>
                </div>

                {{-- Court list --}}
                <div class="court-list" id="courtList">
                    <div style="text-align:center;padding:20px;color:var(--text-muted);font-size:13px;">Pilih tanggal untuk melihat lapangan</div>
                </div>
            </div>
        </div>

        {{-- RIGHT: Slots --}}
        <div class="right-col">
            <div class="slot-panel" id="slotPanel">
                <div class="slot-panel-title" id="slotPanelTitle">Bandeja Court</div>

                <div class="slot-empty" id="slotEmpty">
                    <div style="font-size:32px;margin-bottom:8px;">🏸</div>
                    <div>Pilih lapangan untuk melihat<br>ketersediaan slot</div>
                </div>

                <div style="display:none;" id="slotContent">
                    <div class="slot-legend">
                        <span><span class="legend-dot ld-sel"></span>Dipilih</span>
                        <span><span class="legend-dot ld-avail"></span>Tersedia</span>
                        <span><span class="legend-dot ld-booked"></span>Terisi</span>
                        <span><span class="legend-dot ld-maint"></span>Maintenance</span>
                    </div>
                    <div class="slot-grid" id="slotGrid"></div>
                </div>
            </div>

            {{-- Selected summary + Next button --}}
            <div id="selectionSummary" style="display:none;background:var(--cream-card);border-radius:16px;padding:16px 18px;border:1px solid rgba(0,0,0,0.06);">
                <div style="font-size:11px;font-weight:800;color:var(--text-muted);text-transform:uppercase;letter-spacing:.1em;margin-bottom:10px;">Pilihan Anda</div>
                <div id="summaryContent"></div>
            </div>

            <button class="next-btn" id="nextBtn" disabled onclick="goToBooking()">
                Lanjutkan →
            </button>
        </div>
    </div>
</div>

{{-- Hidden form to POST to booking --}}
<form id="bookingForm" method="GET" action="{{ route('booking.index') }}" style="display:none;">
    <input type="hidden" name="court_id"       id="fCourtId">
    <input type="hidden" name="tanggal_booking" id="fDate">
    <input type="hidden" name="jam_mulai"      id="fStart">
    <input type="hidden" name="jam_selesai"    id="fEnd">
</form>
@endsection

@push('scripts')
<script>
// ═══════════ STATE ═══════════
const today = new Date();
let selectedDate   = null;
let calBaseMonth   = today.getMonth();
let calBaseYear    = today.getFullYear();
let courtsData     = [];
let activeCourt    = null;
let selectedSlots  = []; // [{start, end, price, courtId, courtName}]

const DAYS_ID    = ['Min','Sen','Sel','Rab','Kam','Jum','Sab'];
const MONTHS_ID  = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
const MONTHS_FULL= ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
const DOW_SHORT  = ['Min','Sen','Sel','Rab','Kam','Jum','Sab'];

// ═══════════ CALENDAR ═══════════
function toggleCal() {
    const p = document.getElementById('calPopup');
    p.classList.toggle('show');
    if (p.classList.contains('show')) renderCal();
}
document.addEventListener('click', e => {
    const cal = document.getElementById('calPopup');
    const btn = document.getElementById('dateDisplay');
    const calBtn = document.querySelector('.cal-btn');
    if (!cal.contains(e.target) && !btn.contains(e.target) && !calBtn.contains(e.target)) {
        cal.classList.remove('show');
    }
});
document.getElementById('calPrev').onclick = () => { calBaseMonth--; if(calBaseMonth<0){calBaseMonth=11;calBaseYear--;} renderCal(); };
document.getElementById('calNext').onclick = () => { calBaseMonth++; if(calBaseMonth>11){calBaseMonth=0;calBaseYear++;} renderCal(); };

function renderCal() {
    const wrap = document.getElementById('calMonths');
    wrap.innerHTML = '';
    for (let m = 0; m < 2; m++) {
        let mo = calBaseMonth + m, yr = calBaseYear;
        if (mo > 11) { mo -= 12; yr++; }
        const div = document.createElement('div');
        div.className = 'cal-month';
        div.innerHTML = `<div class="cal-month-title">${MONTHS_FULL[mo]} ${yr}</div>`;
        const grid = document.createElement('div');
        grid.className = 'cal-grid';
        DOW_SHORT.forEach(d => { const c = document.createElement('div'); c.className='cal-dow'; c.textContent=d; grid.appendChild(c); });
        const first = new Date(yr, mo, 1).getDay();
        for (let i=0;i<first;i++) { const e=document.createElement('div'); e.className='cal-day empty'; grid.appendChild(e); }
        const days = new Date(yr, mo+1, 0).getDate();
        for (let d=1;d<=days;d++) {
            const cell = document.createElement('div');
            const thisDate = new Date(yr, mo, d);
            const isToday = thisDate.toDateString()===today.toDateString();
            const isPast  = thisDate < new Date(today.getFullYear(), today.getMonth(), today.getDate());
            const isSel   = selectedDate && thisDate.toDateString()===selectedDate.toDateString();
            cell.className = `cal-day${isToday?' today':''}${isPast?' past':''}${isSel?' selected':''}`;
            cell.textContent = d;
            if (!isPast) cell.onclick = () => pickDate(new Date(yr, mo, d));
            grid.appendChild(cell);
        }
        div.appendChild(grid);
        wrap.appendChild(div);
    }
}

function pickDate(d) {
    selectedDate = d;
    const dow = DAYS_ID[d.getDay()];
    const mon = MONTHS_ID[d.getMonth()];
    document.getElementById('labelDay').textContent   = dow;
    document.getElementById('labelDate').textContent  = d.getDate();
    document.getElementById('labelMonth').textContent = mon;
    document.getElementById('dayNameDisplay').textContent = `${dow}, ${d.getDate()} ${mon} ${d.getFullYear()}`;
    document.getElementById('calPopup').classList.remove('show');
    selectedSlots = [];
    activeCourt   = null;
    loadAvailability();
}

// ═══════════ AVAILABILITY ═══════════
async function loadAvailability() {
    const dateStr = `${selectedDate.getFullYear()}-${String(selectedDate.getMonth()+1).padStart(2,'0')}-${String(selectedDate.getDate()).padStart(2,'0')}`;
    document.getElementById('courtList').innerHTML = '<div style="text-align:center;padding:20px;color:var(--text-muted);font-size:13px;">Memuat...</div>';
    try {
        const res  = await fetch(`/courts/availability?date=${dateStr}`);
        const data = await res.json();
        courtsData = data.courts;
        renderFloorPlan();
        renderCourtList();
        renderSlotEmpty();
    } catch {
        document.getElementById('courtList').innerHTML = '<div style="color:#C0392B;padding:12px;font-size:13px;">Gagal memuat data.</div>';
    }
}

// ═══════════ FLOOR PLAN ═══════════
function renderFloorPlan() {
    const fp = document.getElementById('floorPlan');
    fp.innerHTML = '';
    courtsData.forEach(c => {
        const tile = document.createElement('div');
        tile.className = `court-tile${c.status_lapangan==='maintenance'?' maintenance':''}${activeCourt&&activeCourt.id===c.id?' selected':''}`;
        tile.innerHTML = `<span class="court-tile-label">${c.nama_lapangan}</span>`;
        if (c.status_lapangan !== 'maintenance') tile.onclick = () => selectCourt(c);
        fp.appendChild(tile);
    });
}

// ═══════════ COURT LIST ═══════════
function renderCourtList() {
    const list = document.getElementById('courtList');
    list.innerHTML = '';
    courtsData.forEach(c => {
        const row = document.createElement('div');
        row.className = `court-row${activeCourt&&activeCourt.id===c.id?' active':''}`;
        const isMaint = c.status_lapangan === 'maintenance';
        row.innerHTML = `
            <span class="court-row-name">${c.nama_lapangan}</span>
            <span class="court-row-status ${isMaint?'status-maintenance':'status-available'}">
                ${isMaint ? 'Maintenance' : 'Tersedia'}
            </span>`;
        if (!isMaint) row.onclick = () => selectCourt(c);
        list.appendChild(row);
    });
}

// ═══════════ SELECT COURT ═══════════
function selectCourt(c) {
    activeCourt = c;
    renderFloorPlan();
    renderCourtList();
    renderSlots(c);
}

function renderSlotEmpty() {
    document.getElementById('slotEmpty').style.display   = 'block';
    document.getElementById('slotContent').style.display = 'none';
}

// ═══════════ SLOT GRID ═══════════
function renderSlots(court) {
    document.getElementById('slotPanelTitle').textContent = court.nama_lapangan;
    document.getElementById('slotEmpty').style.display    = 'none';
    document.getElementById('slotContent').style.display  = 'block';

    const grid = document.getElementById('slotGrid');
    grid.innerHTML = '';

    court.slots.forEach(slot => {
        const isSel = selectedSlots.some(s => s.courtId===court.id && s.start===slot.start);
        const cell  = document.createElement('div');
        cell.className = `slot-cell ${slot.status}${isSel?' selected':''}`;
        cell.innerHTML = `
            <span class="slot-time">${slot.start}</span>
            <span class="slot-time">– ${slot.end}</span>
            <span class="slot-price">${slot.status==='available'?'Rp'+fmtRp(slot.price):(slot.status==='booked'?'Terisi':'N/A')}</span>
        `;
        if (slot.status === 'available') {
            cell.onclick = () => toggleSlot(court, slot, cell);
        }
        grid.appendChild(cell);
    });
}

function toggleSlot(court, slot, cell) {
    const idx = selectedSlots.findIndex(s => s.courtId===court.id && s.start===slot.start);
    if (idx >= 0) {
        selectedSlots.splice(idx, 1);
        cell.classList.remove('selected');
    } else {
        // Only allow contiguous slots on same court for a clean booking
        // Clear if switching court
        if (selectedSlots.length && selectedSlots[0].courtId !== court.id) selectedSlots = [];
        selectedSlots.push({ courtId: court.id, courtName: court.nama_lapangan, start: slot.start, end: slot.end, price: slot.price });
        cell.classList.add('selected');
    }
    updateSummary();
}

// ═══════════ SUMMARY + NEXT ═══════════
function updateSummary() {
    const btn     = document.getElementById('nextBtn');
    const summDiv = document.getElementById('selectionSummary');
    const content = document.getElementById('summaryContent');

    if (!selectedSlots.length) {
        summDiv.style.display = 'none';
        btn.disabled = true;
        return;
    }

    summDiv.style.display = 'block';
    btn.disabled = false;

    // Sort slots by start
    const sorted = [...selectedSlots].sort((a,b) => a.start.localeCompare(b.start));
    const totalPrice = sorted.reduce((s,x) => s + x.price, 0);
    const startTime  = sorted[0].start;
    const endTime    = sorted[sorted.length-1].end;
    const courtName  = sorted[0].courtName;

    const dateStr = selectedDate ? `${DAYS_ID[selectedDate.getDay()]}, ${selectedDate.getDate()} ${MONTHS_ID[selectedDate.getMonth()]}` : '';

    content.innerHTML = `
        <div style="font-size:14px;font-weight:700;color:var(--text-dark);margin-bottom:4px;">${courtName}</div>
        <div style="font-size:12px;color:var(--text-muted);margin-bottom:2px;">📅 ${dateStr}</div>
        <div style="font-size:12px;color:var(--text-muted);margin-bottom:10px;">⏰ ${startTime} – ${endTime} (${sorted.length} jam)</div>
        <div style="font-size:15px;font-weight:800;color:var(--green-deep);">Rp${fmtRp(totalPrice)}</div>
    `;
}

function goToBooking() {
    if (!selectedSlots.length || !selectedDate) return;
    const sorted = [...selectedSlots].sort((a,b) => a.start.localeCompare(b.start));
    const dateStr = `${selectedDate.getFullYear()}-${String(selectedDate.getMonth()+1).padStart(2,'0')}-${String(selectedDate.getDate()).padStart(2,'0')}`;
    document.getElementById('fCourtId').value = sorted[0].courtId;
    document.getElementById('fDate').value    = dateStr;
    document.getElementById('fStart').value   = sorted[0].start;
    document.getElementById('fEnd').value     = sorted[sorted.length-1].end;
    document.getElementById('bookingForm').submit();
}

// ═══════════ UTIL ═══════════
function fmtRp(n) {
    return Number(n).toLocaleString('id-ID');
}

// Init: pick today
pickDate(today);
</script>
@endpush
