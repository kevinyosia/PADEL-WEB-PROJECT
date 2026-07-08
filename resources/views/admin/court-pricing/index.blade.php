@extends('layouts.admin')

@section('content')
<div style="max-width: 1280px; margin: 0 auto;">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h2 class="text-2xl font-bold mb-6">Kelola Harga Lapangan</h2>
                
                @if(session('success'))
                    <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-700 rounded">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="overflow-x-auto">
                    <table class="min-w-full border-collapse border border-gray-300">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="border border-gray-300 px-4 py-2 text-left">Nama Lapangan</th>
                                <th class="border border-gray-300 px-4 py-2 text-right">Weekday Normal</th>
                                <th class="border border-gray-300 px-4 py-2 text-right">Weekday Prime (16:00-22:00)</th>
                                <th class="border border-gray-300 px-4 py-2 text-right">Weekend Normal</th>
                                <th class="border border-gray-300 px-4 py-2 text-right">Weekend Prime (16:00-22:00)</th>
                                <th class="border border-gray-300 px-4 py-2 text-center">Status</th>
                                <th class="border border-gray-300 px-4 py-2 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($courts as $court)
                                <tr class="hover:bg-gray-50">
                                    <td class="border border-gray-300 px-4 py-2">{{ $court->nama_lapangan }}</td>
                                    <td class="border border-gray-300 px-4 py-2 text-right">Rp {{ number_format($court->harga_pagi_tengahmalam, 0, ',', '.') }}</td>
                                    <td class="border border-gray-300 px-4 py-2 text-right">Rp {{ number_format($court->harga_malam, 0, ',', '.') }}</td>
                                    <td class="border border-gray-300 px-4 py-2 text-right">Rp {{ number_format($court->harga_weekend, 0, ',', '.') }}</td>
                                    <td class="border border-gray-300 px-4 py-2 text-right">Rp {{ number_format($court->harga_weekend_prime, 0, ',', '.') }}</td>
                                    <td class="border border-gray-300 px-4 py-2 text-center">
                                        <span class="px-2 py-1 rounded text-sm {{ $court->status === 'tersedia' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ ucfirst($court->status) }}
                                        </span>
                                    </td>
                                    <td class="border border-gray-300 px-4 py-2 text-center">
                                        <a href="{{ route('admin.pricing.edit', $court) }}" class="text-blue-600 hover:text-blue-800 font-medium">Edit</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="border border-gray-300 px-4 py-2 text-center text-gray-500">
                                        Tidak ada lapangan yang ditemukan
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
