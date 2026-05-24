@extends('layouts.admin')

@section('content')
<style>
    .page-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 28px;
    }
    .page-header h1 { font-size: 26px; font-weight: 800; color: #0F172A; }
    .page-header p  { font-size: 13px; color: #64748B; margin-top: 4px; }
    
    .btn-back {
        padding: 9px 16px;
        background: #fff;
        border: 1px solid #E2E8F0;
        border-radius: 10px;
        color: #475569;
        font-size: 13px;
        font-weight: 700;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.15s;
    }
    .btn-back:hover { background: #F8FAFC; color: #0F172A; border-color: #CBD5E1; }

    /* Profile Card & Details layout */
    .detail-grid {
        display: grid;
        grid-template-columns: 320px 1fr;
        gap: 24px;
        align-items: start;
    }

    .profile-card {
        background: #fff;
        border: 1px solid #E2E8F0;
        border-radius: 14px;
        padding: 28px 24px;
        text-align: center;
    }
    .large-avatar {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: linear-gradient(135deg, #4F46E5, #818CF8);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        font-weight: 800;
        color: #fff;
        margin-bottom: 16px;
        overflow: hidden;
    }
    .large-avatar img { width: 100%; height: 100%; object-fit: cover; }
    
    .profile-name { font-size: 18px; font-weight: 800; color: #0F172A; }
    .profile-email { font-size: 12px; color: #64748B; margin-top: 4px; word-break: break-all; }
    
    .profile-badges { display: flex; justify-content: center; gap: 8px; margin-top: 12px; flex-wrap: wrap; }
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    .status-badge::before { content: ''; width: 5px; height: 5px; border-radius: 50%; background: currentColor; }
    .badge-active { background: #ECFDF5; color: #047857; }
    .badge-banned { background: #FEF2F2; color: #B91C1C; }
    .badge-membership { background: #FFFBEB; color: #B45309; border: 1px solid #FDE68A; }

    .divider { height: 1px; background: #F1F5F9; margin: 20px 0; }

    .profile-info-list { text-align: left; }
    .info-item { margin-bottom: 14px; }
    .info-label { font-size: 10px; font-weight: 700; color: #94A3B8; text-transform: uppercase; letter-spacing: 0.05em; }
    .info-value { font-size: 13px; font-weight: 600; color: #334155; margin-top: 3px; }

    .action-btn-group { display: flex; flex-direction: column; gap: 8px; margin-top: 20px; }
    .btn-profile-action {
        width: 100%;
        padding: 10px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
        text-align: center;
        border: 1px solid #E2E8F0;
        background: #fff;
        color: #475569;
        transition: all 0.15s;
        text-decoration: none;
    }
    .btn-profile-action:hover { background: #F8FAFC; color: #0F172A; }
    .btn-profile-ban { background: #FEF2F2; border-color: #FCA5A5; color: #EF4444; }
    .btn-profile-ban:hover { background: #FEE2E2; color: #DC2626; }
    .btn-profile-unban { background: #ECFDF5; border-color: #A7F3D0; color: #10B981; }
    .btn-profile-unban:hover { background: #D1FAE5; color: #059669; }

    /* Summary cards grid */
    .summary-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }
    .summary-card {
        background: #fff;
        border: 1px solid #E2E8F0;
        border-radius: 12px;
        padding: 16px;
        display: flex;
        flex-direction: column;
    }
    .summary-label { font-size: 10px; font-weight: 700; color: #94A3B8; text-transform: uppercase; letter-spacing: 0.05em; }
    .summary-val { font-size: 18px; font-weight: 800; color: #0F172A; margin-top: 6px; }

    /* Tab Layout */
    .tab-container {
        background: #fff;
        border: 1px solid #E2E8F0;
        border-radius: 14px;
        overflow: hidden;
    }
    .tab-headers {
        display: flex;
        background: #F8FAFC;
        border-bottom: 1px solid #E2E8F0;
        padding: 0 10px;
    }
    .tab-header {
        padding: 16px 20px;
        font-size: 13px;
        font-weight: 700;
        color: #64748B;
        background: none;
        border: none;
        border-bottom: 2px solid transparent;
        cursor: pointer;
        outline: none;
        transition: all 0.15s;
    }
    .tab-header:hover { color: #0F172A; }
    .tab-header.active { color: #4F46E5; border-bottom-color: #4F46E5; }

    .tab-content { padding: 24px; display: none; }
    .tab-content.active { display: block; }

    /* Tables */
    .data-table { width: 100%; border-collapse: collapse; text-align: left; }
    .data-table th {
        font-size: 11px;
        font-weight: 700;
        color: #475569;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        padding: 10px 14px;
        border-bottom: 1.5px solid #E2E8F0;
    }
    .data-table td {
        font-size: 13px;
        color: #334155;
        padding: 12px 14px;
        border-bottom: 1px solid #F1F5F9;
        vertical-align: middle;
    }
    .data-table tr:last-child td { border-bottom: none; }

    .pembayaran-badge {
        display: inline-flex;
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
    }
    .badge-lunas { background: #D1FAE5; color: #065F46; }
    .badge-belum_lunas { background: #FEF3C7; color: #92400E; }

    .reservasi-badge {
        display: inline-flex;
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
    }
    .badge-res-confirmed { background: #DBEAFE; color: #1E40AF; }
    .badge-res-pending { background: #F3E8FF; color: #6B21A8; }
    .badge-res-cancelled { background: #F3F4F6; color: #374151; }

    .points-tag { font-weight: 700; }
    .points-positive { color: #10B981; }
    .points-negative { color: #EF4444; }

    /* Modal for ban reason */
    .modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(15,23,42,0.5);
        z-index: 9000;
        align-items: center;
        justify-content: center;
    }
    .modal-overlay.show { display: flex; }
    .modal-dialog {
        background: #fff;
        border-radius: 16px;
        padding: 28px;
        width: 100%;
        max-width: 440px;
        box-shadow: 0 20px 60px rgba(0,0,0,0.15);
    }
    .modal-title { font-size: 18px; font-weight: 800; color: #0F172A; margin-bottom: 8px; }
    .modal-desc { font-size: 13px; color: #64748B; line-height: 1.5; margin-bottom: 20px; }
    
    .form-group { margin-bottom: 16px; }
    .form-label { display: block; font-size: 12px; font-weight: 700; color: #475569; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.05em; }
    .form-textarea {
        width: 100%;
        height: 90px;
        padding: 10px 12px;
        border: 1.5px solid #E2E8F0;
        border-radius: 8px;
        font-size: 13px;
        font-family: 'Figtree', sans-serif;
        color: #334155;
        resize: none;
        outline: none;
    }
    .form-textarea:focus { border-color: #4F46E5; }

    .modal-actions { display: flex; gap: 10px; }
    .btn-modal-cancel {
        flex: 1;
        padding: 11px;
        background: #F1F5F9;
        color: #475569;
        border: none;
        border-radius: 9px;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
    }
    .btn-modal-cancel:hover { background: #E2E8F0; }
    .btn-modal-submit {
        flex: 1;
        padding: 11px;
        background: #4F46E5;
        color: #fff;
        border: none;
        border-radius: 9px;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
    }
    .btn-modal-submit:hover { background: #4338CA; }
    .btn-modal-danger { background: #EF4444; }
    .btn-modal-danger:hover { background: #DC2626; }
</style>

{{-- Header --}}
<div class="page-header">
    <div>
        <h1 style="display: flex; align-items: center; gap: 10px;">
            <a href="{{ route('admin.users.index') }}" class="btn-back">← Kembali</a>
            Detail User
        </h1>
        <p>Rekam jejak aktivitas, riwayat keuangan, poin reward, dan kepatuhan UU PDP</p>
    </div>
</div>

<div class="detail-grid">
    {{-- Left Column: Profile Card --}}
    <div class="profile-card">
        <div class="large-avatar">
            @if($user->photo)
                <img src="{{ asset('storage/' . $user->photo) }}" alt="Foto Profile">
            @else
                {{ strtoupper(substr($user->name, 0, 1)) }}
            @endif
        </div>
        <div class="profile-name">{{ $user->name }}</div>
        <div class="profile-email">{{ $user->email }}</div>
        
        <div class="profile-badges">
            @if($user->isBanned())
                <span class="status-badge badge-banned">Diblokir</span>
            @else
                <span class="status-badge badge-active">Aktif</span>
            @endif

            @if($user->membership)
                <span class="status-badge badge-membership">Member</span>
            @endif
        </div>

        <div class="divider"></div>

        <div class="profile-info-list">
            <div class="info-item">
                <div class="info-label">Telepon</div>
                <div class="info-value">{{ $user->phone ?? '-' }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Poin Aktif</div>
                <div class="info-value">{{ $user->membership->total_poin_aktif ?? 0 }} Poin</div>
            </div>
            <div class="info-item">
                <div class="info-label">Poin Terpakai</div>
                <div class="info-value">{{ $user->membership->total_poin_terpakai ?? 0 }} Poin</div>
            </div>
            <div class="info-item">
                <div class="info-label">Bergabung Pada</div>
                <div class="info-value">{{ $user->created_at->format('d M Y, H:i') }}</div>
            </div>
            @if($user->isBanned())
                <div class="info-item" style="background: #FFF5F5; padding: 10px; border-radius: 8px; border: 1px solid #FED7D7;">
                    <div class="info-label" style="color: #E53E3E;">Alasan Blokir</div>
                    <div class="info-value" style="color: #C53030; font-size: 12px; margin-top: 2px;">{{ $user->banned_reason }}</div>
                    <div class="info-value" style="color: #718096; font-size: 10px; margin-top: 4px;">Pada: {{ $user->banned_at->format('d M Y, H:i') }}</div>
                </div>
            @endif
        </div>

        <div class="action-btn-group">
            @if($user->isBanned())
                <form action="{{ route('admin.users.unban', $user) }}" method="POST" style="width: 100%;">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn-profile-action btn-profile-unban">Buka Blokir Akun</button>
                </form>
            @else
                <button type="button" class="btn-profile-action btn-profile-ban" onclick="openBanModal('{{ $user->id }}')">Blokir Akun</button>
            @endif

            <button type="button" class="btn-profile-action" onclick="openAnonymizeModal('{{ $user->id }}')" style="border-color: #FCA5A5; color: #EF4444;">
                Hapus & Anonimkan (UU PDP)
            </button>
        </div>
    </div>

    {{-- Right Column: Activity History & Financial stats --}}
    <div>
        {{-- Summary Cards --}}
        <div class="summary-grid">
            <div class="summary-card">
                <span class="summary-label">Total Reservasi</span>
                <span class="summary-val">{{ $transactionSummary['total_reservations'] }} kali</span>
            </div>
            <div class="summary-card">
                <span class="summary-label">Total Pengeluaran</span>
                <span class="summary-val" style="color: #4F46E5;">Rp {{ number_format($transactionSummary['total_spent'] + $transactionSummary['total_membership_spending'], 0, ',', '.') }}</span>
            </div>
            <div class="summary-card">
                <span class="summary-label">Sewa Lapangan</span>
                <span class="summary-val">Rp {{ number_format($transactionSummary['total_court_spending'], 0, ',', '.') }}</span>
            </div>
            <div class="summary-card">
                <span class="summary-label">Rental / Layanan Coach</span>
                <span class="summary-val">Rp {{ number_format($transactionSummary['total_coach_spending'] + $transactionSummary['total_equipment_spending'], 0, ',', '.') }}</span>
            </div>
        </div>

        {{-- Tabbed tables container --}}
        <div class="tab-container">
            <div class="tab-headers">
                <button class="tab-header active" onclick="switchTab(event, 'tab-reservations')">Riwayat Reservasi</button>
                <button class="tab-header" onclick="switchTab(event, 'tab-points')">Riwayat Poin</button>
                <button class="tab-header" onclick="switchTab(event, 'tab-membership')">Pembayaran Member</button>
            </div>

            {{-- Tab: Reservations --}}
            <div class="tab-content active" id="tab-reservations">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID & Tanggal</th>
                            <th>Detail Lapangan</th>
                            <th>Coach / Alat</th>
                            <th>Total Tagihan</th>
                            <th>Status Pembayaran</th>
                            <th>Status Reservasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($user->reservations as $res)
                            <tr>
                                <td>
                                    <div style="font-weight: 700; color: #0F172A;">#{{ $res->id }}</div>
                                    <div style="font-size: 11px; color: #64748B; margin-top: 2px;">{{ \Carbon\Carbon::parse($res->tanggal_booking)->format('d M Y') }}</div>
                                </td>
                                <td>
                                    <div style="font-weight: 600;">{{ $res->court->nama_lapangan ?? 'Court' }}</div>
                                    <div style="font-size: 11px; color: #64748B; margin-top: 2px;">{{ substr($res->jam_mulai, 0, 5) }} - {{ substr($res->jam_selesai, 0, 5) }}</div>
                                </td>
                                <td>
                                    @if($res->coach)
                                        <div style="font-size: 12px; font-weight: 600; color: #4F46E5;">Coach: {{ $res->coach->user->name }}</div>
                                    @endif
                                    @foreach($res->equipment as $eq)
                                        <div style="font-size: 11px; color: #475569; margin-top: 1px;">⚙️ {{ $eq->nama_alat }} ({{ $res->pivot->jumlah_sewa ?? $eq->pivot->jumlah_sewa ?? 1 }}x)</div>
                                    @endforeach
                                    @if(!$res->coach && count($res->equipment) === 0)
                                        <span style="color: #94A3B8; font-style: italic;">Tanpa Coach/Alat</span>
                                    @endif
                                </td>
                                <td>
                                    <div style="font-weight: 700;">Rp {{ number_format($res->transaction->grand_total ?? 0, 0, ',', '.') }}</div>
                                    @if($res->transaction && $res->transaction->potongan_poin > 0)
                                        <div style="font-size: 10px; color: #EF4444; margin-top: 2px;">Potongan: -Rp {{ number_format($res->transaction->potongan_poin, 0, ',', '.') }}</div>
                                    @endif
                                </td>
                                <td>
                                    @if($res->transaction)
                                        <span class="pembayaran-badge badge-{{ $res->transaction->status_pembayaran }}">
                                            {{ $res->transaction->status_pembayaran === 'lunas' ? 'Lunas' : 'Belum Lunas' }}
                                        </span>
                                        <div style="font-size: 10px; color: #94A3B8; margin-top: 2px;">{{ strtoupper($res->transaction->metode_pembayaran) }}</div>
                                    @else
                                        <span class="pembayaran-badge badge-belum_lunas">Belum Ada Transaksi</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="reservasi-badge badge-res-{{ $res->status_reservasi }}">
                                        {{ $res->status_reservasi }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="text-align: center; padding: 40px; color: #94A3B8;">
                                    Belum ada riwayat reservasi lapangan untuk user ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Tab: Points --}}
            <div class="tab-content" id="tab-points">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Deskripsi Aktivitas</th>
                            <th>Perubahan Poin</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($user->pointHistories as $ph)
                            <tr>
                                <td>{{ $ph->created_at->format('d M Y, H:i') }}</td>
                                <td>{{ $ph->deskripsi ?? 'Perolehan poin dari transaksi' }}</td>
                                <td>
                                    @if($ph->tipe === 'tambah' || $ph->points > 0)
                                        <span class="points-tag points-positive">+{{ $ph->points ?? 0 }} Poin</span>
                                    @else
                                        <span class="points-tag points-negative">{{ $ph->points ?? 0 }} Poin</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" style="text-align: center; padding: 40px; color: #94A3B8;">
                                    Belum ada riwayat perolehan atau pemakaian poin reward.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Tab: Membership Payments --}}
            <div class="tab-content" id="tab-membership">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Jumlah Pembayaran</th>
                            <th>Tanggal Pembayaran</th>
                            <th>Status Pembayaran</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($user->membershipPayments as $mp)
                            <tr>
                                <td style="font-weight: 700; color: #0F172A;">{{ $mp->order_id }}</td>
                                <td style="font-weight: 700;">Rp {{ number_format($mp->amount, 0, ',', '.') }}</td>
                                <td>{{ $mp->paid_at ? $mp->paid_at->format('d M Y, H:i') : '-' }}</td>
                                <td>
                                    <span class="pembayaran-badge badge-{{ $mp->status === 'paid' ? 'lunas' : 'belum_lunas' }}">
                                        {{ $mp->status === 'paid' ? 'Paid' : $mp->status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" style="text-align: center; padding: 40px; color: #94A3B8;">
                                    Belum ada riwayat transaksi pendaftaran membership.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Ban Modal --}}
<div class="modal-overlay" id="banModal">
    <div class="modal-dialog">
        <h3 class="modal-title">Blokir Akun</h3>
        <p class="modal-desc">Akun yang diblokir tidak akan bisa masuk ke dalam sistem. Silakan isi alasan pemblokiran.</p>
        
        <form id="banForm" method="POST">
            @csrf
            @method('PATCH')
            <div class="form-group">
                <label for="ban_reason" class="form-label">Alasan Pemblokiran</label>
                <textarea 
                    name="reason" 
                    id="ban_reason" 
                    class="form-textarea" 
                    placeholder="Contoh: Sering no-show reservasi tanpa pembatalan..." 
                    required
                ></textarea>
            </div>
            
            <div class="modal-actions">
                <button type="button" class="btn-modal-cancel" onclick="closeBanModal()">Batal</button>
                <button type="submit" class="btn-modal-submit btn-modal-danger">Blokir Sekarang</button>
            </div>
        </form>
    </div>
</div>

{{-- Anonymize Modal --}}
<div class="modal-overlay" id="anonymizeModal">
    <div class="modal-dialog">
        <h3 class="modal-title">Anonimkan & Hapus Data (UU PDP)</h3>
        <p class="modal-desc" style="color: #EF4444; font-weight: 600;">⚠️ PERINGATAN: Tindakan ini permanen!</p>
        <p class="modal-desc">
            Sesuai UU Pelindungan Data Pribadi (UU PDP), data personal user (nama, email, telepon, foto) akan dihapus secara permanen atau disamarkan. 
            Data transaksi, reservasi, dan keuangan akan tetap dipertahankan untuk kebutuhan pembukuan secara anonim tanpa merusak relasi database.
        </p>
        
        <form id="anonymizeForm" method="POST">
            @csrf
            @method('DELETE')
            
            <div class="modal-actions">
                <button type="button" class="btn-modal-cancel" onclick="closeAnonymizeModal()">Batal</button>
                <button type="submit" class="btn-modal-submit btn-modal-danger">Hapus & Anonimkan</button>
            </div>
        </form>
    </div>
</div>

<script>
    function switchTab(e, tabId) {
        document.querySelectorAll('.tab-header').forEach(btn => btn.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
        
        e.target.classList.add('active');
        document.getElementById(tabId).classList.add('active');
    }

    function openBanModal(userId) {
        const form = document.getElementById('banForm');
        form.action = `/admin/users/${userId}/ban`;
        document.getElementById('ban_reason').value = '';
        document.getElementById('banModal').classList.add('show');
    }
    
    function closeBanModal() {
        document.getElementById('banModal').classList.remove('show');
    }

    function openAnonymizeModal(userId) {
        const form = document.getElementById('anonymizeForm');
        form.action = `/admin/users/${userId}`;
        document.getElementById('anonymizeModal').classList.add('show');
    }
    
    function closeAnonymizeModal() {
        document.getElementById('anonymizeModal').classList.remove('show');
    }

    // Close on overlay backdrop click
    document.querySelectorAll('.modal-overlay').forEach(overlay => {
        overlay.addEventListener('click', function(e) {
            if (e.target === this) {
                closeBanModal();
                closeAnonymizeModal();
            }
        });
    });
</script>
@endsection
