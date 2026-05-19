@extends('layouts.admin')

@section('content')
<style>
        .page-header {
            display: flex; align-items: flex-start;
            justify-content: space-between; margin-bottom: 28px;
        }
        .page-header h1 { font-size: 26px; font-weight: 800; color: #0F172A; }
        .page-header p  { font-size: 13px; color: #64748B; margin-top: 4px; }

        .btn-primary {
            display: inline-flex; align-items: center; gap: 7px;
            padding: 10px 20px; background: #2563EB; color: #fff;
            border: none; border-radius: 10px;
            font-size: 13px; font-weight: 700; font-family: 'Figtree', sans-serif;
            text-decoration: none; cursor: pointer;
            transition: all 0.15s; white-space: nowrap;
        }
        .btn-primary:hover { background: #1D4ED8; box-shadow: 0 4px 14px rgba(37,99,235,0.3); }

        /* ─── Stats bar ─── */
        .stats-bar { display: flex; gap: 14px; margin-bottom: 28px; flex-wrap: wrap; }
        .stat-pill {
            display: flex; align-items: center; gap: 8px;
            background: #fff; border: 1px solid #E2E8F0;
            border-radius: 10px; padding: 10px 18px;
            font-size: 13px; font-weight: 600; color: #374151;
        }
        .stat-pill .pill-num { font-size: 20px; font-weight: 800; color: #0F172A; }
        .stat-dot { width: 8px; height: 8px; border-radius: 50%; }
        .dot-blue   { background: #3B82F6; box-shadow: 0 0 0 2px rgba(59,130,246,0.2); }
        .dot-green  { background: #10B981; box-shadow: 0 0 0 2px rgba(16,185,129,0.2); }
        .dot-gray   { background: #94A3B8; box-shadow: 0 0 0 2px rgba(148,163,184,0.2); }
        .dot-amber  { background: #F59E0B; box-shadow: 0 0 0 2px rgba(245,158,11,0.2); }

        /* ─── Grid ─── */
        .coaches-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        /* ─── Coach Card ─── */
        .coach-card {
            background: #fff; border: 1px solid #E2E8F0;
            border-radius: 14px; overflow: hidden;
            transition: box-shadow 0.2s;
            display: flex; flex-direction: column;
        }
        .coach-card:hover { box-shadow: 0 4px 20px rgba(0,0,0,0.08); }

        .card-header {
            padding: 18px 20px 14px;
            border-bottom: 1px solid #F1F5F9;
            display: flex; align-items: flex-start; justify-content: space-between; gap: 12px;
        }
        .coach-avatar {
            width: 44px; height: 44px; border-radius: 50%;
            background: linear-gradient(135deg, #2563EB, #60A5FA);
            display: flex; align-items: center; justify-content: center;
            font-size: 17px; font-weight: 800; color: #fff;
            flex-shrink: 0;
        }
        .coach-info { flex: 1; min-width: 0; }
        .coach-name {
            font-size: 15px; font-weight: 800; color: #0F172A;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        .coach-contact { font-size: 11px; color: #94A3B8; margin-top: 3px; }

        /* Availability badge */
        .avail-badge {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 4px 10px; border-radius: 20px;
            font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;
            flex-shrink: 0;
        }
        .avail-badge::before { content:''; width:5px; height:5px; border-radius:50%; background:currentColor; }
        .badge-active   { background:#DCFCE7; color:#166534; }
        .badge-inactive { background:#F1F5F9; color:#64748B; }
        .badge-on_leave { background:#FEF9C3; color:#854D0E; }

        .card-body { padding: 16px 20px; flex: 1; }

        .expertise-text {
            font-size: 12px; color: #475569; line-height: 1.55;
            display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;
            overflow: hidden; margin-bottom: 14px;
        }

        .rate-row {
            display: flex; align-items: center; justify-content: space-between;
            background: #F8FAFC; border: 1px solid #E2E8F0;
            border-radius: 8px; padding: 9px 12px; margin-bottom: 14px;
        }
        .rate-label { font-size: 10px; font-weight: 700; color: #94A3B8; text-transform: uppercase; letter-spacing: 0.06em; }
        .rate-value { font-size: 14px; font-weight: 800; color: #0F172A; }

        /* Schedule buttons */
        .schedule-wrap { margin-bottom: 4px; }
        .schedule-label { font-size: 10px; font-weight: 700; color: #94A3B8; text-transform: uppercase; letter-spacing: 0.06em; margin-bottom: 7px; }
        .schedule-days { display: flex; gap: 5px; align-items: center; }
        .day-btn {
            width: 30px; height: 30px; border-radius: 7px;
            font-size: 10px; font-weight: 700; text-transform: uppercase;
            display: flex; align-items: center; justify-content: center;
            border: none;
        }
        .day-btn.day-on  { background: #DBEAFE; color: #1D4ED8; }
        .day-btn.day-off { background: #F1F5F9; color: #CBD5E1; }
        .days-count {
            margin-left: 6px; font-size: 11px; font-weight: 700; color: #64748B;
            white-space: nowrap;
        }

        .card-footer { padding: 14px 20px; border-top: 1px solid #F1F5F9; }

        /* Status selector */
        .status-selector-wrap { position: relative; margin-bottom: 10px; }
        .status-selector {
            width: 100%; appearance: none; -webkit-appearance: none;
            padding: 9px 36px 9px 14px;
            border: 1.5px solid #E2E8F0; border-radius: 8px;
            font-size: 12px; font-weight: 600; font-family: 'Figtree', sans-serif;
            color: #374151; background: #F8FAFC;
            cursor: pointer; outline: none; transition: border-color 0.15s;
        }
        .status-selector:focus { border-color: #2563EB; box-shadow: 0 0 0 3px rgba(37,99,235,0.1); }
        .selector-chevron {
            position: absolute; right: 12px; top: 50%;
            transform: translateY(-50%);
            pointer-events: none; color: #94A3B8; font-size: 11px;
        }

        /* Action buttons */
        .action-row { display: flex; gap: 8px; }
        .btn-edit {
            flex: 1; padding: 8px; text-align: center;
            background: #EFF6FF; color: #2563EB;
            border: 1px solid #BFDBFE; border-radius: 8px;
            font-size: 12px; font-weight: 700;
            text-decoration: none; transition: all 0.15s;
        }
        .btn-edit:hover { background: #DBEAFE; }
        .btn-delete {
            padding: 8px 14px;
            background: #FEF2F2; color: #EF4444;
            border: 1px solid #FECACA; border-radius: 8px;
            font-size: 12px; font-weight: 700; font-family: 'Figtree', sans-serif;
            cursor: pointer; transition: all 0.15s;
        }
        .btn-delete:hover { background: #FEE2E2; }

        /* Confirm delete overlay (pure CSS + JS toggle) */
        .delete-confirm {
            display: none; position: fixed; inset: 0;
            background: rgba(15,23,42,0.5); z-index: 9000;
            align-items: center; justify-content: center;
        }
        .delete-confirm.show { display: flex; }
        .delete-dialog {
            background: #fff; border-radius: 16px; padding: 32px;
            width: 380px; text-align: center;
            box-shadow: 0 20px 60px rgba(0,0,0,0.2);
        }
        .delete-dialog h3 { font-size: 18px; font-weight: 800; color: #0F172A; margin-bottom: 8px; }
        .delete-dialog p { font-size: 13px; color: #64748B; line-height: 1.55; margin-bottom: 24px; }
        .delete-dialog-actions { display: flex; gap: 10px; }
        .btn-cancel-confirm {
            flex: 1; padding: 11px;
            background: #F1F5F9; color: #475569;
            border: none; border-radius: 9px;
            font-size: 13px; font-weight: 700; font-family: 'Figtree', sans-serif;
            cursor: pointer;
        }
        .btn-cancel-confirm:hover { background: #E2E8F0; }
        .btn-delete-confirm {
            flex: 1; padding: 11px;
            background: #EF4444; color: #fff;
            border: none; border-radius: 9px;
            font-size: 13px; font-weight: 700; font-family: 'Figtree', sans-serif;
            cursor: pointer;
        }
        .btn-delete-confirm:hover { background: #DC2626; }

        .empty-state { grid-column: 1/-1; background:#fff; border:1px solid #E2E8F0; border-radius:14px; padding:64px; text-align:center; }
        .empty-state .empty-icon { font-size: 40px; margin-bottom: 12px; }
        .empty-state p { color: #94A3B8; font-size: 14px; }
    </style>

    {{-- ─── Page Header ─── --}}
    <div class="page-header">
        <div>
            <h1>Coaches Management</h1>
            <p>Coaching Roster — Manage professional schedules and staff availability</p>
        </div>
        <a href="{{ route('admin.coaches.create') }}" class="btn-primary">
            <span>+</span> Register New Coach
        </a>
    </div>

    {{-- ─── Stats Bar ─── --}}
    <div class="stats-bar">
        <div class="stat-pill">
            <span class="stat-dot dot-blue"></span>
            <span class="pill-num">{{ $stats['total'] }}</span>
            Total Coaches
        </div>
        <div class="stat-pill">
            <span class="stat-dot dot-green"></span>
            <span class="pill-num">{{ $stats['active'] }}</span>
            Active
        </div>
        <div class="stat-pill">
            <span class="stat-dot dot-gray"></span>
            <span class="pill-num">{{ $stats['inactive'] }}</span>
            Inactive
        </div>
        <div class="stat-pill">
            <span class="stat-dot dot-amber"></span>
            <span class="pill-num">{{ $stats['on_leave'] }}</span>
            On Leave
        </div>
    </div>

    {{-- ─── Coaches Grid ─── --}}
    <div class="coaches-grid">
        @forelse($coaches as $coach)
            @php
                $avColor = $coach->getAvailabilityColor();
                $badgeClass = match($avColor) {
                    'green' => 'badge-active',
                    'amber' => 'badge-on_leave',
                    default => 'badge-inactive',
                };
                $days = ['mon'=>'M','tue'=>'T','wed'=>'W','thu'=>'T','fri'=>'F'];
            @endphp

            <div class="coach-card">
                {{-- Header --}}
                <div class="card-header">
                    <div class="coach-avatar">
                        @if($coach->photo)
                            <img 
                                src="{{ asset('storage/' . $coach->photo) }}" 
                                alt="Coach Photo" 
                                style="width:100%; height:100%; object-fit:cover; border-radius:50%;"
                            >
                        @else
                            {{ strtoupper(substr($coach->user->name ?? 'C', 0, 1)) }}
                        @endif
                    </div>
                    <div class="coach-info">
                        <div class="coach-name">{{ $coach->user->name }}</div>
                        <div class="coach-contact">{{ $coach->user->phone }}</div>
                        <div class="coach-contact" style="color:#BFDBFE;font-size:10px;">{{ $coach->user->email }}</div>
                    </div>
                    <span class="avail-badge {{ $badgeClass }}">
                        {{ $coach->getAvailabilityLabel() }}
                    </span>
                </div>

                {{-- Body --}}
                <div class="card-body">
                    <div class="expertise-text">{{ $coach->deskripsi_keahlian }}</div>

                    <div class="rate-row">
                        <span class="rate-label">Hourly Rate</span>
                        <span class="rate-value">Rp {{ number_format($coach->harga_per_jam, 0, ',', '.') }}</span>
                    </div>

                    <div class="schedule-wrap">
                        <div class="schedule-label">Weekly Schedule &nbsp;·&nbsp; {{ $coach->getActiveDaysCount() }}/5 Days</div>
                        <div class="schedule-days">
                            @foreach($days as $key => $label)
                                <span class="day-btn {{ $coach->isAvailableOnDay($key) ? 'day-on' : 'day-off' }}">
                                    {{ $label }}
                                </span>
                            @endforeach
                            <span class="days-count">{{ $coach->getActiveDaysCount() }}/5</span>
                        </div>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="card-footer">
                    {{-- Availability Dropdown --}}
                    <form action="{{ route('admin.coaches.update-availability', $coach) }}" method="POST" style="margin-bottom:10px;">
                        @csrf
                        @method('PATCH')
                        <div class="status-selector-wrap">
                            <select name="availability_status" class="status-selector" onchange="this.form.submit()">
                                <option value="active"    {{ $coach->availability_status === 'active'    ? 'selected' : '' }}>⬤ Active</option>
                                <option value="inactive"  {{ $coach->availability_status === 'inactive'  ? 'selected' : '' }}>⬤ Inactive</option>
                                <option value="on_leave"  {{ $coach->availability_status === 'on_leave'  ? 'selected' : '' }}>⬤ On Leave</option>
                            </select>
                            <span class="selector-chevron">▼</span>
                        </div>
                    </form>

                    {{-- Edit + Delete --}}
                    <div class="action-row">
                        <a href="{{ route('admin.coaches.edit', $coach) }}" class="btn-edit">✏ Edit</a>
                        <button
                            type="button"
                            class="btn-delete"
                            onclick="openDeleteConfirm('{{ $coach->id }}', '{{ addslashes($coach->user->name) }}')"
                        >🗑</button>
                    </div>
                </div>
            </div>
        @empty
            <div class="empty-state">
                <div class="empty-icon">👥</div>
                <p>Belum ada coach yang terdaftar.</p>
                <a href="{{ route('admin.coaches.create') }}" class="btn-primary" style="margin-top:16px;display:inline-flex;">+ Register Coach</a>
            </div>
        @endforelse
    </div>

    {{-- ─── Delete Confirm Modal ─── --}}
    <div class="delete-confirm" id="deleteConfirmModal">
        <div class="delete-dialog">
            <div style="font-size:36px;margin-bottom:12px;">⚠️</div>
            <h3>Hapus Coach?</h3>
            <p id="deleteConfirmText">Akun coach dan semua datanya akan dihapus permanen. Tindakan ini tidak bisa dibatalkan.</p>
            <div class="delete-dialog-actions">
                <button type="button" class="btn-cancel-confirm" onclick="closeDeleteConfirm()">Batal</button>
                <form id="deleteForm" method="POST" style="flex:1;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-delete-confirm" style="width:100%;">Hapus Sekarang</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openDeleteConfirm(coachId, coachName) {
            document.getElementById('deleteConfirmText').textContent =
                `Akun "${coachName}" dan semua datanya akan dihapus permanen. Tindakan ini tidak bisa dibatalkan.`;
            document.getElementById('deleteForm').action =
                `/admin/coaches/${coachId}`;
            document.getElementById('deleteConfirmModal').classList.add('show');
        }
        function closeDeleteConfirm() {
            document.getElementById('deleteConfirmModal').classList.remove('show');
        }
        // Close on backdrop click
        document.getElementById('deleteConfirmModal').addEventListener('click', function(e) {
            if (e.target === this) closeDeleteConfirm();
        });
    </script>
@endsection
