@extends('layouts.manager')

@section('content')
<div class="space-y-8">
    {{-- ─── Header ─── --}}
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-[#0f172a]">Dashboard Manajemen</h1>
            <p class="text-sm text-gray-500 mt-2">Metrik performa operasional Arena</p>
        </div>
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
        
        <div class="h-64 bg-gradient-to-b from-blue-50 to-transparent rounded-lg flex items-end justify-around px-6 py-4 gap-2">
            @foreach($revenueData as $day)
            @php
                $bookingHeight = $day['bookings'] > 0 ? max(12, round(($day['bookings'] / $chartMaxBookings) * 144)) : 4;
                $salesHeight = $day['sales'] > 0 ? max(12, round(($day['sales'] / $chartMaxSales) * 144)) : 4;
            @endphp
            <div class="flex h-full flex-col items-center justify-end gap-2">
                <div class="flex h-40 items-end gap-1.5">
                    <div
                        class="w-5 rounded-t-lg bg-gradient-to-t from-blue-500 to-blue-300 {{ $day['bookings'] > 0 ? 'opacity-80' : 'opacity-30' }}"
                        style="height: {{ $bookingHeight }}px"
                        title="Bookings: {{ $day['bookings'] }}"
                    ></div>
                    <div
                        class="w-5 rounded-t-lg bg-gradient-to-t from-amber-500 to-amber-300 {{ $day['sales'] > 0 ? 'opacity-80' : 'opacity-30' }}"
                        style="height: {{ $salesHeight }}px"
                        title="Sales: Rp{{ number_format($day['sales'], 0) }}"
                    ></div>
                </div>
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

    {{-- ─── Recent Activity ─── --}}
    <div>
        {{-- Recent Activity --}}
        <div class="bg-white rounded-xl p-8 border border-gray-100 shadow-sm">
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
