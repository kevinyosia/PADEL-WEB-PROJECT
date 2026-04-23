@extends('layouts.admin')

@section('content')
<style>
        .page-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 28px; }
        .page-header h1 { font-size: 26px; font-weight: 800; color: #0F172A; }
        .page-header p { font-size: 13px; color: #64748B; margin-top: 4px; }
        
        .courts-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; }
        
        .court-card {
            background: #fff; border: 1px solid #E2E8F0;
            border-radius: 14px; padding: 20px; transition: box-shadow 0.2s;
        }
        .court-card:hover { box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
        
        .court-name { font-size: 16px; font-weight: 800; color: #0F172A; margin-bottom: 8px; }
        .court-desc { font-size: 12px; color: #64748B; margin-bottom: 14px; }
        
        .status-badge {
            display: inline-block; padding: 6px 12px; border-radius: 20px;
            font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;
            margin-bottom: 14px;
        }
        .badge-tersedia { background: #DCFCE7; color: #166534; }
        .badge-maintenance { background: #FEE2E2; color: #991B1B; }
        .badge-pembersihan { background: #FEF9C3; color: #854D0E; }
        
        .booking-row {
            background: #F8FAFC; border: 1px solid #E2E8F0;
            border-radius: 10px; padding: 12px; margin-bottom: 10px;
        }
        .booking-label { font-size: 10px; color: #94A3B8; text-transform: uppercase; font-weight: 700; }
        .booking-user { font-size: 14px; font-weight: 800; color: #0F172A; margin-top: 4px; }
        .booking-time { font-size: 11px; color: #64748B; margin-top: 2px; }
        
        .status-form { margin-top: 14px; display: flex; gap: 8px; }
        .status-select {
            flex: 1; padding: 10px 14px; border: 1.5px solid #E2E8F0;
            border-radius: 9px; font-size: 12px; font-weight: 600;
            color: #374151; background: #F8FAFC; cursor: pointer;
        }
        .status-select:focus { border-color: #2563EB; box-shadow: 0 0 0 3px rgba(37,99,235,0.1); }
    </style>

    <div class="page-header">
        <div>
            <h1>Court Management</h1>
            <p>Operational Overview — Manage court status and bookings</p>
        </div>
    </div>

    <div class="courts-grid">
        @forelse($courts as $court)
            <div class="court-card">
                <div class="court-name">{{ $court['nama_lapangan'] }}</div>
                
                @php
                    $badgeClass = match($court['status_lower']) {
                        'tersedia' => 'badge-tersedia',
                        'maintenance' => 'badge-maintenance',
                        'pembersihan' => 'badge-pembersihan',
                        default => 'badge-tersedia',
                    };
                @endphp
                <span class="status-badge {{ $badgeClass }}">{{ $court['status'] }}</span>

                <div class="booking-row">
                    <div class="booking-label">Current Booking</div>
                    @if($court['current_booking'])
                        <div class="booking-user">{{ $court['current_booking']['user_name'] }}</div>
                        <div class="booking-time">{{ $court['current_booking']['time'] }}</div>
                    @else
                        <div class="booking-time">No active bookings</div>
                    @endif
                </div>

                @if($court['next_booking'])
                    <div class="booking-row">
                        <div class="booking-label">Next Booking</div>
                        <div class="booking-time">{{ $court['next_booking']['time'] }}</div>
                    </div>
                @endif

                <form action="{{ route('admin.courts.update-status', $court['id']) }}" method="POST" class="status-form">
                    @csrf
                    @method('PATCH')
                    <select name="status" class="status-select" onchange="this.form.submit()">
                        <option value="tersedia" {{ $court['status_lower'] === 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                        <option value="maintenance" {{ $court['status_lower'] === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                        <option value="pembersihan" {{ $court['status_lower'] === 'pembersihan' ? 'selected' : '' }}>Pembersihan</option>
                    </select>
                </form>
            </div>
        @empty
            <div style="grid-column:1/-1;background:#fff;border:1px solid #E2E8F0;border-radius:14px;padding:48px;text-align:center;">
                <p style="color:#94A3B8;">Tidak ada lapangan yang ditemukan</p>
            </div>
        @endforelse
    </div>
@endsection
