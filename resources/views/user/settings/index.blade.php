@extends('layouts.user')
@section('title', 'Settings — Bandeja Padel Arena')

@push('styles')
<style>
    :root {
        --green-deep: #2D4A1E; --green-mid: #3A5C28;
        --cream-bg: #EDE8D8; --cream-card: #F5F1E6; --cream-white: #FAFAF5;
        --text-dark: #1A1A0F; --text-muted: #6B6B5A;
    }
    .page-wrap { min-height: 100vh; background: var(--cream-bg); padding: 28px 32px; }
    .page-title { font-family: 'DM Serif Display', serif; font-size: 28px; color: var(--text-dark); margin-bottom: 24px; }

    /* ── Main menu view ── */
    .settings-menu { display: flex; flex-direction: column; gap: 10px; max-width: 480px; }
    .settings-item {
        display: flex; align-items: center; gap: 16px;
        background: var(--cream-card); border-radius: 16px; padding: 18px 20px;
        cursor: pointer; text-decoration: none; transition: all .18s;
        border: 1px solid rgba(0,0,0,0.06);
    }
    .settings-item:hover { background: #EDE8D8; transform: translateX(4px); }
    .settings-icon {
        width: 42px; height: 42px; border-radius: 12px;
        background: #E8E3D3; display: flex; align-items: center; justify-content: center;
        font-size: 18px; flex-shrink: 0;
    }
    .settings-label { font-size: 16px; font-weight: 700; color: var(--text-dark); }
    .settings-arrow { margin-left: auto; color: var(--text-muted); font-size: 16px; }

    .logout-btn {
        display: block; width: 100%; max-width: 480px;
        padding: 14px; margin-top: 20px;
        background: #E53935; color: #fff;
        border: none; border-radius: 50px;
        font-size: 15px; font-weight: 800; font-family: 'Figtree', sans-serif;
        cursor: pointer; transition: background .15s;
    }
    .logout-btn:hover { background: #C62828; }

    /* ── Sub-page ── */
    .sub-page { display: none; }
    .sub-page.active { display: block; }
    .sub-back {
        display: inline-flex; align-items: center; gap: 7px;
        font-size: 13px; font-weight: 700; color: var(--text-muted);
        cursor: pointer; margin-bottom: 20px; background: none; border: none;
        transition: color .15s;
    }
    .sub-back:hover { color: var(--text-dark); }
    .sub-title { font-family: 'DM Serif Display', serif; font-size: 24px; color: var(--text-dark); margin-bottom: 22px; }

    /* History table */
    .history-card { background: var(--cream-card); border-radius: 16px; overflow: hidden; border: 1px solid rgba(0,0,0,0.06); max-width: 540px; }
    .history-tab  { background: var(--green-deep); color: #fff; font-size: 12px; font-weight: 700; padding: 9px 16px; border-radius: 20px; display: inline-block; margin-bottom: 14px; }
    .history-table { width: 100%; border-collapse: collapse; }
    .history-table td { padding: 14px 18px; border-bottom: 1px solid rgba(0,0,0,0.05); font-size: 13px; color: var(--text-dark); }
    .history-table tbody tr:last-child td { border-bottom: none; }
    .history-table tbody tr:hover td { background: #EDE8D8; }
    .history-date  { color: var(--text-muted); font-weight: 600; white-space: nowrap; }
    .history-desc  { font-weight: 600; }
    .history-price { font-weight: 800; text-align: right; white-space: nowrap; }

    /* Change password form */
    .pw-card { background: var(--cream-card); border-radius: 16px; padding: 24px 28px; border: 1px solid rgba(0,0,0,0.06); max-width: 440px; }
    .pw-field { margin-bottom: 16px; }
    .pw-label { font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: .07em; display: block; margin-bottom: 7px; }
    .pw-input {
        width: 100%; padding: 11px 14px;
        border: 1.5px solid rgba(0,0,0,0.1); border-radius: 10px;
        font-size: 14px; font-family: 'Figtree', sans-serif; color: var(--text-dark);
        background: var(--cream-white); outline: none; transition: border-color .15s;
    }
    .pw-input:focus { border-color: var(--green-mid); box-shadow: 0 0 0 3px rgba(58,92,40,0.1); }
    .pw-input.is-error { border-color: #C0392B; background: #FEF2F2; }
    .pw-error { font-size: 11px; color: #C0392B; margin-top: 4px; }
    .pw-btn {
        width: 100%; padding: 13px; background: var(--green-deep); color: #fff;
        border: none; border-radius: 50px; margin-top: 8px;
        font-size: 14px; font-weight: 700; font-family: 'Figtree', sans-serif;
        cursor: pointer; transition: background .15s;
    }
    .pw-btn:hover { background: var(--green-mid); }

    /* Terms */
    .terms-card { background: var(--cream-card); border-radius: 16px; padding: 28px 32px; border: 1px solid rgba(0,0,0,0.06); max-width: 640px; }
    .terms-card h2 { font-family: 'DM Serif Display', serif; font-size: 20px; color: var(--text-dark); margin-bottom: 16px; }
    .terms-card p, .terms-card li { font-size: 13px; color: var(--text-muted); line-height: 1.75; margin-bottom: 12px; }
    .terms-card h3 { font-size: 14px; font-weight: 800; color: var(--text-dark); margin-top: 18px; margin-bottom: 6px; }
    .terms-card ul { padding-left: 18px; }
    .terms-footer { font-size: 11px; color: #B0AA98; margin-top: 20px; text-align: center; }
</style>
@endpush

@section('content')
<div class="page-wrap">

    {{-- ── MAIN MENU ── --}}
    <div id="viewMain">
        <div class="page-title">Settings</div>

        <div class="settings-menu">
            <a href="#" class="settings-item" onclick="showSub('history');return false;">
                <div class="settings-icon">🕐</div>
                <span class="settings-label">My History</span>
                <span class="settings-arrow">›</span>
            </a>
            <a href="#" class="settings-item" onclick="showSub('password');return false;">
                <div class="settings-icon">🔒</div>
                <span class="settings-label">Change Password</span>
                <span class="settings-arrow">›</span>
            </a>
            <a href="#" class="settings-item" onclick="showSub('terms');return false;">
                <div class="settings-icon">📋</div>
                <span class="settings-label">Terms & Conditions</span>
                <span class="settings-arrow">›</span>
            </a>
        </div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="logout-btn">Log Out</button>
        </form>
    </div>

    {{-- ── MY HISTORY ── --}}
    <div id="viewHistory" class="sub-page">
        <button class="sub-back" onclick="showMain()">← My History</button>

        <div class="history-tab">Payment History</div>

        <div class="history-card">
            @if($reservations->count())
            <table class="history-table">
                <tbody>
                    @foreach($reservations as $res)
                    @php
                        $courtName  = $res->court->nama_lapangan ?? '—';
                        $hasCoach   = $res->coach_id ? ', Coach' : '';
                        $hasEquip   = $res->equipment->count() ? ', Rent' : '';
                        $label      = $courtName . $hasEquip . $hasCoach;
                        $total      = $res->transaction->grand_total ?? 0;
                    @endphp
                    <tr>
                        <td class="history-date">{{ \Carbon\Carbon::parse($res->tanggal_booking)->format('d F Y') }}</td>
                        <td class="history-desc">{{ $label }}</td>
                        <td class="history-price">Rp{{ number_format($total,0,',','.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div style="padding:36px;text-align:center;color:var(--text-muted);font-size:13px;">Belum ada riwayat pembayaran.</div>
            @endif
        </div>
    </div>

    {{-- ── CHANGE PASSWORD ── --}}
    <div id="viewPassword" class="sub-page">
        <button class="sub-back" onclick="showMain()">← Change Password</button>

        <div class="pw-card">
            @if(session('password_updated'))
            <div style="background:#E6EDD8;border:1px solid #C2DEB0;border-radius:10px;padding:11px 14px;margin-bottom:16px;font-size:13px;color:var(--green-deep);font-weight:700;">
                ✓ Password berhasil diperbarui.
            </div>
            @endif

            <form method="POST" action="{{ route('settings.password') }}">
                @csrf
                @method('PATCH')

                <div class="pw-field">
                    <label class="pw-label" for="current_password">Old Password</label>
                    <input type="password" id="current_password" name="current_password"
                        class="pw-input {{ $errors->updatePassword->has('current_password') ? 'is-error' : '' }}"
                        autocomplete="current-password">
                    @error('current_password', 'updatePassword')
                        <div class="pw-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="pw-field">
                    <label class="pw-label" for="password">New Password</label>
                    <input type="password" id="password" name="password"
                        class="pw-input {{ $errors->updatePassword->has('password') ? 'is-error' : '' }}"
                        autocomplete="new-password">
                    @error('password', 'updatePassword')
                        <div class="pw-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="pw-field">
                    <label class="pw-label" for="password_confirmation">Confirm New Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation"
                        class="pw-input" autocomplete="new-password">
                </div>

                <button type="submit" class="pw-btn">Confirm</button>
            </form>
        </div>
    </div>

    {{-- ── TERMS & CONDITIONS ── --}}
    <div id="viewTerms" class="sub-page">
        <button class="sub-back" onclick="showMain()">← Terms & Conditions</button>

        <div class="terms-card">
            <h2>Syarat & Ketentuan Penggunaan</h2>
            <p>Dengan menggunakan layanan Bandeja Padel Arena, Anda menyetujui syarat dan ketentuan berikut. Harap baca dengan saksama sebelum melakukan pemesanan.</p>

            <h3>1. Pemesanan & Pembayaran</h3>
            <ul>
                <li>Pemesanan lapangan bersifat mengikat setelah konfirmasi pembayaran diterima.</li>
                <li>Pembayaran wajib dilakukan melalui saluran resmi yang tersedia di platform.</li>
                <li>Harga yang tertera adalah harga per jam sesuai slot waktu yang dipilih.</li>
            </ul>

            <h3>2. Pembatalan & Pengembalian Dana</h3>
            <ul>
                <li>Pembatalan dapat dilakukan minimal 24 jam sebelum jadwal untuk mendapatkan pengembalian dana penuh.</li>
                <li>Pembatalan kurang dari 24 jam tidak mendapatkan pengembalian dana.</li>
                <li>Pengembalian dana diproses dalam 3–7 hari kerja.</li>
            </ul>

            <h3>3. Tata Tertib Fasilitas</h3>
            <ul>
                <li>Pengguna wajib menjaga kebersihan dan ketertiban selama berada di area arena.</li>
                <li>Penggunaan lapangan hanya diperbolehkan sesuai slot waktu yang dipesan.</li>
                <li>Dilarang membawa makanan/minuman ke area lapangan kecuali air minum.</li>
            </ul>

            <h3>4. Privasi Data</h3>
            <p>Data pribadi Anda digunakan semata-mata untuk keperluan pengelolaan pemesanan dan tidak akan dibagikan kepada pihak ketiga tanpa persetujuan Anda.</p>

            <div class="terms-footer">© 2026 Bandeja Padel Arena. Seluruh hak cipta dilindungi.</div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
function showSub(name) {
    document.getElementById('viewMain').style.display = 'none';
    document.querySelectorAll('.sub-page').forEach(el => el.classList.remove('active'));
    document.getElementById('view' + name.charAt(0).toUpperCase() + name.slice(1)).classList.add('active');
}
function showMain() {
    document.getElementById('viewMain').style.display = '';
    document.querySelectorAll('.sub-page').forEach(el => el.classList.remove('active'));
}

// Auto-open password sub-page if there were validation errors from that form
@if($errors->updatePassword->any())
showSub('password');
@endif

@if(session('password_updated'))
showSub('password');
@endif
</script>
@endpush
