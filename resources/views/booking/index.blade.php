@extends('layouts.customer')

@section('title', 'Booking - Bandeja Padel Arena')

@section('content')
    <div class="flex flex-col gap-8 pb-10">
        <div class="w-full h-40 bg-gray-200 rounded-2xl flex items-center justify-center shadow-inner">
            <span class="text-lg font-bold text-gray-500">Booking Arena</span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="bg-[#f4f4f4] rounded-2xl p-6 shadow-sm lg:col-span-2">
                <h3 class="text-sm font-bold text-gray-700 mb-4 uppercase tracking-widest">Pilihan Lapangan</h3>
                <div class="space-y-3">
                    @forelse($courts as $court)
                        <div class="bg-white rounded-xl p-4 border border-gray-200">
                            <p class="font-semibold text-gray-800">{{ $court->nama_lapangan }}</p>
                            <p class="text-sm text-gray-600">Status: {{ $court->status }}</p>
                        </div>
                    @empty
                        <p class="text-gray-500">Belum ada lapangan tersedia.</p>
                    @endforelse
                </div>
            </div>

            <div class="bg-[#f4f4f4] rounded-2xl p-6 shadow-sm">
                <h3 class="text-sm font-bold text-gray-700 mb-4 uppercase tracking-widest">Slot Jam</h3>
                <div class="max-h-80 overflow-y-auto grid grid-cols-2 gap-2">
                    @foreach($timeSlots as $slot)
                        <span class="text-xs font-semibold text-gray-700 bg-white border border-gray-200 rounded-lg px-2 py-2 text-center">{{ $slot }}</span>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-[#f4f4f4] rounded-2xl p-6 shadow-sm">
                <h3 class="text-sm font-bold text-gray-700 mb-4 uppercase tracking-widest">Coach</h3>
                <ul class="space-y-2 text-sm text-gray-700">
                    @forelse($coaches as $coach)
                        <li class="bg-white border border-gray-200 rounded-lg px-3 py-2">Coach #{{ $coach->id }} - Rp{{ number_format($coach->harga_per_jam, 0, ',', '.') }}/jam</li>
                    @empty
                        <li>Tidak ada coach tersedia.</li>
                    @endforelse
                </ul>
            </div>

            <div class="bg-[#f4f4f4] rounded-2xl p-6 shadow-sm">
                <h3 class="text-sm font-bold text-gray-700 mb-4 uppercase tracking-widest">Peralatan</h3>
                <ul class="space-y-2 text-sm text-gray-700">
                    @forelse($equipments as $equipment)
                        <li class="bg-white border border-gray-200 rounded-lg px-3 py-2">{{ $equipment->nama_alat }} - Rp{{ number_format($equipment->harga, 0, ',', '.') }}</li>
                    @empty
                        <li>Tidak ada peralatan tersedia.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
@endsection
