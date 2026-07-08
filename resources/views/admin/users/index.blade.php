@extends('layouts.admin')

@section('content')
<style>
    .page-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        margin-bottom: 28px;
    }
    .page-header h1 { font-size: 26px; font-weight: 800; color: #0F172A; }
    .page-header p  { font-size: 13px; color: #64748B; margin-top: 4px; }

    /* Stats bar */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 20px;
        margin-bottom: 28px;
    }
    .stat-card {
        background: #fff;
        border: 1px solid #E2E8F0;
        border-radius: 14px;
        padding: 20px;
        display: flex;
        align-items: center;
        gap: 16px;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 20px rgba(0,0,0,0.05);
    }
    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
    }
    .icon-total { background: #EEF2FF; color: #4F46E5; }
    .icon-active { background: #ECFDF5; color: #10B981; }
    .icon-banned { background: #FEF2F2; color: #EF4444; }
    .icon-member { background: #FFFBEB; color: #F59E0B; }

    .stat-info { display: flex; flex-direction: column; }
    .stat-val { font-size: 22px; font-weight: 800; color: #0F172A; }
    .stat-label { font-size: 12px; font-weight: 600; color: #64748B; margin-top: 2px; }

    /* Control panel (search and filter) */
    .control-panel {
        background: #fff;
        border: 1px solid #E2E8F0;
        border-radius: 14px;
        padding: 16px 20px;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
    }
    .search-form { display: flex; gap: 8px; flex: 1; max-width: 420px; }
    .search-input-wrap { position: relative; flex: 1; }
    .search-input {
        width: 100%;
        padding: 10px 14px 10px 36px;
        border: 1.5px solid #E2E8F0;
        border-radius: 10px;
        font-size: 13px;
        font-family: 'Figtree', sans-serif;
        color: #334155;
        outline: none;
        transition: border-color 0.15s;
    }
    .search-input:focus { border-color: #4F46E5; }
    .search-icon {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #94A3B8;
        font-size: 14px;
        pointer-events: none;
    }
    .btn-search {
        padding: 10px 18px;
        background: #0F172A;
        color: #fff;
        border: none;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        transition: background 0.15s;
    }
    .btn-search:hover { background: #1E293B; }
    .btn-clear {
        padding: 10px 14px;
        background: #F1F5F9;
        color: #475569;
        border: none;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    .btn-clear:hover { background: #E2E8F0; }

    /* Table styles */
    .table-container {
        background: #fff;
        border: 1px solid #E2E8F0;
        border-radius: 14px;
        overflow: hidden;
        margin-bottom: 24px;
    }
    .users-table { width: 100%; border-collapse: collapse; text-align: left; }
    .users-table th {
        background: #F8FAFC;
        padding: 14px 20px;
        font-size: 11px;
        font-weight: 700;
        color: #475569;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        border-bottom: 1px solid #E2E8F0;
    }
    .users-table td {
        padding: 16px 20px;
        font-size: 13px;
        color: #334155;
        border-bottom: 1px solid #F1F5F9;
        vertical-align: middle;
    }
    .users-table tr:last-child td { border-bottom: none; }
    .user-profile-cell { display: flex; align-items: center; gap: 12px; }
    .user-avatar {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        background: linear-gradient(135deg, #4F46E5, #818CF8);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        font-weight: 800;
        color: #fff;
        flex-shrink: 0;
        overflow: hidden;
    }
    .user-avatar img { width: 100%; height: 100%; object-fit: cover; }
    .user-name-info { display: flex; flex-direction: column; }
    .user-display-name { font-weight: 700; color: #0F172A; text-decoration: none; }
    .user-display-name:hover { color: #4F46E5; }
    .user-display-email { font-size: 11px; color: #64748B; margin-top: 2px; }

    /* Badges */
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

    /* Actions */
    .table-actions { display: flex; gap: 8px; }
    .btn-action-icon {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #E2E8F0;
        background: #fff;
        color: #475569;
        cursor: pointer;
        transition: all 0.15s;
        text-decoration: none;
        font-size: 14px;
    }
    .btn-action-icon:hover { background: #F8FAFC; color: #0F172A; border-color: #CBD5E1; }
    .btn-action-ban:hover { background: #FEF2F2; color: #EF4444; border-color: #FCA5A5; }
    .btn-action-unban:hover { background: #ECFDF5; color: #10B981; border-color: #6EE7B7; }
    .btn-action-delete:hover { background: #FEF2F2; color: #EF4444; border-color: #FCA5A5; }

    /* Pagination container */
    .pagination-wrap { padding: 16px 20px; background: #F8FAFC; border-top: 1px solid #E2E8F0; }

    /* Empty state */
    .empty-state { padding: 64px 32px; text-align: center; }
    .empty-icon { font-size: 40px; margin-bottom: 12px; }
    .empty-state p { color: #64748B; font-size: 14px; }

    /* Modal dialog overrides */
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
        <h1>User Management</h1>
        <p>Master User — Kelola akun, keanggotaan, penangguhan, dan kepatuhan privasi (UU PDP)</p>
    </div>
</div>

{{-- Stats Grid --}}
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon icon-total">👥</div>
        <div class="stat-info">
            <span class="stat-val">{{ $stats['total'] }}</span>
            <span class="stat-label">Total Customer</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon icon-active">🟢</div>
        <div class="stat-info">
            <span class="stat-val">{{ $stats['active'] }}</span>
            <span class="stat-label">Akun Aktif</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon icon-banned">🔴</div>
        <div class="stat-info">
            <span class="stat-val">{{ $stats['banned'] }}</span>
            <span class="stat-label">Diblokir</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon icon-member">⭐</div>
        <div class="stat-info">
            <span class="stat-val">{{ $stats['member'] }}</span>
            <span class="stat-label">Member Aktif</span>
        </div>
    </div>
</div>

{{-- Control Panel (Search) --}}
<div class="control-panel">
    <form action="{{ route('admin.users.index') }}" method="GET" class="search-form">
        <div class="search-input-wrap">
            <input 
                type="text" 
                name="search" 
                class="search-input" 
                placeholder="Cari nama, email, atau telepon..." 
                value="{{ $search }}"
            >
            <span class="search-icon">🔍</span>
        </div>
        <button type="submit" class="btn-search">Cari</button>
        @if($search)
            <a href="{{ route('admin.users.index') }}" class="btn-clear">Reset</a>
        @endif
    </form>
</div>

{{-- Table Container --}}
<div class="table-container">
    <table class="users-table">
        <thead>
            <tr>
                <th>Customer</th>
                <th>Telepon</th>
                <th>Status</th>
                <th>Tanggal Bergabung</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
                <tr>
                    <td>
                        <div class="user-profile-cell">
                            <div class="user-avatar">
                                @if($user->photo)
                                    <img src="{{ asset('storage/' . $user->photo) }}" alt="Foto Profile">
                                @else
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                @endif
                            </div>
                            <div class="user-name-info">
                                <a href="{{ route('admin.users.show', $user) }}" class="user-display-name">
                                    {{ $user->name }}
                                </a>
                                <span class="user-display-email">{{ $user->email }}</span>
                            </div>
                        </div>
                    </td>
                    <td>{{ $user->phone ?? '-' }}</td>
                    <td>
                        @if($user->isBanned())
                            <span class="status-badge badge-banned" title="Alasan: {{ $user->banned_reason }}">Diblokir</span>
                        @else
                            <span class="status-badge badge-active">Aktif</span>
                        @endif

                        @if($user->membership)
                            <span class="status-badge badge-membership">Member</span>
                        @endif
                    </td>
                    <td>{{ $user->created_at->format('d M Y') }}</td>
                    <td>
                        <div class="table-actions">
                            <a href="{{ route('admin.users.show', $user) }}" class="btn-action-icon" title="Detail Riwayat">👁</a>
                            
                            @if($user->isBanned())
                                <form action="{{ route('admin.users.unban', $user) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn-action-icon btn-action-unban" title="Buka Blokir">🔓</button>
                                </form>
                            @else
                                <button 
                                    type="button" 
                                    class="btn-action-icon btn-action-ban" 
                                    onclick="openBanModal('{{ $user->id }}', '{{ addslashes($user->name) }}')" 
                                    title="Blokir User"
                                >🚫</button>
                            @endif

                            <button 
                                type="button" 
                                class="btn-action-icon btn-action-delete" 
                                onclick="openAnonymizeModal('{{ $user->id }}', '{{ addslashes($user->name) }}')" 
                                title="Anonimkan & Hapus (UU PDP)"
                            >🗑</button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">
                        <div class="empty-state">
                            <div class="empty-icon">👥</div>
                            <p>Tidak ada data user customer ditemukan.</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Pagination --}}
    @if($users->hasPages())
        <div class="pagination-wrap">
            {{ $users->links() }}
        </div>
    @endif
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
                    placeholder="Contoh: Spamming reservasi, sering tidak datang (no-show)..." 
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
    function openBanModal(userId, userName) {
        const form = document.getElementById('banForm');
        form.action = `/admin/users/${userId}/ban`;
        document.getElementById('ban_reason').value = '';
        document.getElementById('banModal').classList.add('show');
    }
    
    function closeBanModal() {
        document.getElementById('banModal').classList.remove('show');
    }

    function openAnonymizeModal(userId, userName) {
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
