@extends('layouts.manager')

@section('content')
<div class="space-y-8">
    {{-- ─── Header ─── --}}
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-[#0f172a]">Dashboard Manajemen</h1>
            <p class="text-sm text-gray-500 mt-2">Metrik performa operasional Arena</p>
        </div>
        <button class="px-4 py-2 bg-[#2d4533] text-white rounded-lg text-sm font-semibold hover:bg-[#1a2620] transition-colors">
            📊 Export Report
        </button>
    </div>

    {{-- ─── Metrics Cards ─── --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        {{-- Total Bookings Card --}}
        <div class="bg-white rounded-xl p-6 border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex justify-between items-start mb-4">
                <span class="text-3xl">🎫</span>
                <span class="text-xs font-bold px-2 py-1 rounded {{ $bookingTrend >= 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                    {{ $bookingTrend >= 0 ? '+' : '' }}{{ $bookingTrend }}%
                </span>
            </div>
            <p class="text-gray-500 text-xs font-semibold uppercase mb-2">Total Booking</p>
            <p class="text-3xl font-bold text-[#0f172a]">{{ number_format($totalBookings) }}</p>
            <p class="text-xs text-gray-400 mt-3">Target: 1,500/bulan</p>
        </div>

        {{-- Ball Sales Card --}}
        <div class="bg-white rounded-xl p-6 border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex justify-between items-start mb-4">
                <span class="text-3xl">⚽</span>
                <span class="text-xs font-bold px-2 py-1 rounded {{ $ballSalesTrend >= 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                    {{ $ballSalesTrend >= 0 ? '+' : '' }}{{ $ballSalesTrend }}%
                </span>
            </div>
            <p class="text-gray-500 text-xs font-semibold uppercase mb-2">Ball Sales</p>
            <p class="text-3xl font-bold text-[#0f172a]">Rp{{ number_format($ballSales, 0) }}</p>
            <p class="text-xs text-gray-400 mt-3">Inventory: Tinggi</p>
        </div>

        {{-- Racket Rentals Card --}}
        <div class="bg-white rounded-xl p-6 border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex justify-between items-start mb-4">
                <span class="text-3xl">🎾</span>
                <span class="text-xs font-bold px-2 py-1 rounded {{ $racketRentalsTrend >= 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                    {{ $racketRentalsTrend >= 0 ? '+' : '' }}{{ $racketRentalsTrend }}%
                </span>
            </div>
            <p class="text-gray-500 text-xs font-semibold uppercase mb-2">Racket Rentals</p>
            <p class="text-3xl font-bold text-[#0f172a]">{{ number_format($racketRentals) }}</p>
            <p class="text-xs text-gray-400 mt-3">4 damaged, 12 repair</p>
        </div>

        {{-- New Members Card --}}
        <div class="bg-white rounded-xl p-6 border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex justify-between items-start mb-4">
                <span class="text-3xl">👤</span>
                <span class="text-xs font-bold px-2 py-1 rounded bg-blue-100 text-blue-700">
                    +{{ $newMembers }}
                </span>
            </div>
            <p class="text-gray-500 text-xs font-semibold uppercase mb-2">New Members</p>
            <p class="text-3xl font-bold text-[#0f172a]">{{ number_format($newMembers) }}</p>
            <p class="text-xs text-gray-400 mt-3">Signed up from yesterday</p>
        </div>
    </div>

    {{-- ─── Revenue & Usage Trends ─── --}}
    <div class="bg-white rounded-xl p-8 border border-gray-100 shadow-sm">
        <h2 class="text-lg font-bold text-[#0f172a] mb-6">Revenue & Usage Trends</h2>
        <p class="text-xs text-gray-500 mb-6">Korelasi antara booking dan penjualan perlengkapan</p>
        
        {{-- Simple Chart Placeholder --}}
        <div class="h-64 bg-gradient-to-b from-blue-50 to-transparent rounded-lg flex items-end justify-around px-6 py-4 gap-2">
            @foreach($revenueData as $day)
            <div class="flex flex-col items-center gap-2">
                <div class="h-24 bg-gradient-to-t from-blue-400 to-blue-300 rounded-t-lg w-8 opacity-70"></div>
                <span class="text-xs text-gray-500 font-semibold">{{ $day['day'] }}</span>
            </div>
            @endforeach
        </div>

        {{-- Legend --}}
        <div class="flex gap-6 justify-center mt-6 text-xs">
            <div class="flex items-center gap-2">
                <div class="w-3 h-3 rounded-full bg-blue-400"></div>
                <span class="text-gray-600">BOOKINGS</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-3 h-3 rounded-full bg-amber-400"></div>
                <span class="text-gray-600">SALES</span>
            </div>
        </div>
    </div>

    {{-- ─── Recent Activity & Arena Status ─── --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Recent Activity --}}
        <div class="lg:col-span-2 bg-white rounded-xl p-8 border border-gray-100 shadow-sm">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-lg font-bold text-[#0f172a]">Recent Activity</h2>
                <a href="{{ route('manager.reviews') }}" class="text-xs text-blue-600 hover:underline font-semibold">Lihat Semua</a>
            </div>

            <div class="space-y-4">
                @forelse($recentActivity as $activity)
                <div class="flex justify-between items-center pb-4 border-b border-gray-100 last:border-0">
                    <div>
                        <p class="font-semibold text-sm text-[#0f172a]">{{ $activity['activity'] }}</p>
                        <p class="text-xs text-gray-500 mt-1">
                            <span class="inline-block px-2 py-0.5 bg-blue-50 text-blue-700 rounded text-xs mr-2">{{ $activity['category'] }}</span>
                            {{ $activity['time'] }}
                        </p>
                    </div>
                    <p class="font-bold text-sm text-[#0f172a]">Rp{{ number_format($activity['amount'], 0) }}</p>
                </div>
                @empty
                <p class="text-xs text-gray-500 text-center py-6">Belum ada aktivitas</p>
                @endforelse
            </div>
        </div>

        {{-- Arena Status --}}
        <div class="bg-gradient-to-br from-[#2d4533] to-[#1a2620] text-white rounded-xl p-8 shadow-lg">
            <h2 class="text-lg font-bold mb-8 flex items-center gap-2">
                🏟️ Arena Status
            </h2>

            <div class="space-y-6">
                {{-- Court Occupancy --}}
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm font-semibold">Court Occupancy</span>
                        <span class="text-xl font-bold">{{ $courtOccupancy }}%</span>
                    </div>
                    <div class="w-full bg-white/20 rounded-full h-2">
                        <div class="bg-green-400 h-2 rounded-full" style="width: {{ $courtOccupancy }}%"></div>
                    </div>
                </div>

                {{-- Peak Hour --}}
                <div>
                    <p class="text-xs uppercase tracking-widest text-white/70 font-bold mb-1">Peak Hour Today</p>
                    <p class="text-2xl font-bold">{{ $peakHourDisplay }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@keyframes slideOut {
    to {
        transform: translateX(400px);
        opacity: 0;
    }
}
</style>
@endsection
