@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Court Management</h1>
                <p class="text-gray-600 mt-1">OPERATIONAL OVERVIEW</p>
            </div>
            <button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                + Book a Court
            </button>
        </div>

        @if(session('success'))
            <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-700 rounded">
                {{ session('success') }}
            </div>
        @endif

        <!-- Courts Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
            @forelse($courts as $court)
                <div class="bg-white rounded-lg shadow-md p-6">
                    <!-- Court Header -->
                    <div class="flex justify-between items-start mb-4">
                        <div class="flex-1">
                            <h3 class="text-xl font-bold text-gray-900">{{ $court['nama_lapangan'] }}</h3>
                            <p class="text-sm text-gray-600 mt-1">{{ $court['description'] }}</p>
                        </div>
                        <div class="text-3xl">🏸</div>
                    </div>

                    <!-- Status Badge -->
                    <div class="mb-4">
                        @php
                            $statusColors = [
                                'available' => 'bg-green-100 text-green-800',
                                'maintenance' => 'bg-red-100 text-red-800',
                                'cleaning' => 'bg-yellow-100 text-yellow-800',
                            ];
                            $statusClass = $statusColors[$court['status_lower']] ?? 'bg-gray-100 text-gray-800';
                        @endphp
                        <span class="inline-block px-3 py-1 rounded-full text-sm font-medium {{ $statusClass }}">
                            {{ $court['status'] }}
                        </span>
                    </div>

                    <!-- Booking Info -->
                    <div class="space-y-3 mb-6 pb-6 border-b border-gray-200">
                        @if($court['current_booking'])
                            <div>
                                <p class="text-xs text-gray-500 uppercase tracking-wide">Current Booking</p>
                                <p class="text-sm font-medium text-gray-900">{{ $court['current_booking']['user_name'] }}</p>
                                <p class="text-xs text-gray-600">{{ $court['current_booking']['time'] }}</p>
                            </div>
                        @else
                            <div>
                                <p class="text-xs text-gray-500 uppercase tracking-wide">Current Booking</p>
                                <p class="text-sm text-gray-600">No active bookings</p>
                            </div>
                        @endif

                        @if($court['next_booking'])
                            <div>
                                <p class="text-xs text-gray-500 uppercase tracking-wide">Next Booking</p>
                                <p class="text-sm font-medium text-gray-900">{{ $court['next_booking']['time'] }} · M. Bwoi</p>
                            </div>
                        @endif
                    </div>

                    <!-- Status Dropdown -->
                    <form action="{{ route('admin.courts.update-status', $court['id']) }}" method="POST" class="flex items-center gap-2">
                        @csrf
                        @method('PATCH')
                        <select 
                            name="status" 
                            onchange="this.form.submit()"
                            class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white text-gray-700 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                            <option value="available" {{ $court['status_lower'] === 'available' ? 'selected' : '' }}>Status: Available</option>
                            <option value="maintenance" {{ $court['status_lower'] === 'maintenance' ? 'selected' : '' }}>Status: Maintenance</option>
                            <option value="cleaning" {{ $court['status_lower'] === 'cleaning' ? 'selected' : '' }}>Status: Cleaning</option>
                        </select>
                    </form>
                </div>
            @empty
                <div class="col-span-full bg-white rounded-lg shadow-md p-12 text-center">
                    <p class="text-gray-500">Tidak ada lapangan yang ditemukan</p>
                </div>
            @endforelse
        </div>

        <!-- Maintenance Log Section -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-bold text-gray-900">Maintenance Log</h2>
                <a href="#" class="text-blue-600 hover:text-blue-800 font-medium text-sm">View full history →</a>
            </div>
            <div class="space-y-4">
                <!-- Placeholder for maintenance logs -->
                <p class="text-gray-500 text-center py-8">Maintenance logs will be displayed here</p>
            </div>
        </div>
    </div>
</div>
@endsection
