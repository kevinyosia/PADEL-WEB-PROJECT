@extends('layouts.admin')

@section('content')
<div class="py-12">
    <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h2 class="text-2xl font-bold mb-6">Kelola Harga Lapangan: {{ $court->nama_lapangan }}</h2>
                
                @if ($errors->any())
                    <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.pricing.update', $court) }}" method="POST">
                    @csrf
                    @method('PATCH')

                    <div class="mb-6">
                        <label for="harga_pagi_tengahmalam" class="block text-gray-700 font-bold mb-2">
                            Weekday Normal (di luar 16:00 - 22:00)
                        </label>
                        <div class="flex items-center">
                            <span class="text-gray-500 mr-2">Rp</span>
                            <input 
                                type="number" 
                                id="harga_pagi_tengahmalam" 
                                name="harga_pagi_tengahmalam" 
                                class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('harga_pagi_tengahmalam') border-red-500 @enderror"
                                value="{{ old('harga_pagi_tengahmalam', $court->harga_pagi_tengahmalam) }}"
                                min="1"
                                required
                            >
                        </div>
                        @error('harga_pagi_tengahmalam')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label for="harga_malam" class="block text-gray-700 font-bold mb-2">
                            Weekday Prime Time (16:00 - 22:00)
                        </label>
                        <div class="flex items-center">
                            <span class="text-gray-500 mr-2">Rp</span>
                            <input 
                                type="number" 
                                id="harga_malam" 
                                name="harga_malam" 
                                class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('harga_malam') border-red-500 @enderror"
                                value="{{ old('harga_malam', $court->harga_malam) }}"
                                min="1"
                                required
                            >
                        </div>
                        @error('harga_malam')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label for="harga_weekend" class="block text-gray-700 font-bold mb-2">
                            Weekend Normal (di luar 16:00 - 22:00)
                        </label>
                        <div class="flex items-center">
                            <span class="text-gray-500 mr-2">Rp</span>
                            <input 
                                type="number" 
                                id="harga_weekend" 
                                name="harga_weekend" 
                                class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('harga_weekend') border-red-500 @enderror"
                                value="{{ old('harga_weekend', $court->harga_weekend) }}"
                                min="1"
                                required
                            >
                        </div>
                        @error('harga_weekend')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label for="harga_weekend_prime" class="block text-gray-700 font-bold mb-2">
                            Weekend Prime Time (16:00 - 22:00)
                        </label>
                        <div class="flex items-center">
                            <span class="text-gray-500 mr-2">Rp</span>
                            <input 
                                type="number" 
                                id="harga_weekend_prime" 
                                name="harga_weekend_prime" 
                                class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('harga_weekend_prime') border-red-500 @enderror"
                                value="{{ old('harga_weekend_prime', $court->harga_weekend_prime) }}"
                                min="1"
                                required
                            >
                        </div>
                        @error('harga_weekend_prime')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="bg-gray-50 p-4 rounded-lg mb-6">
                        <p class="text-sm text-gray-600">
                            <strong>Catatan:</strong> Sistem otomatis memilih harga berdasarkan hari booking dan prime time 16:00 - 22:00. Harga dapat diubah sementara untuk event sesuai persetujuan manajemen.
                        </p>
                    </div>

                    <div class="flex gap-4">
                        <button 
                            type="submit" 
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium"
                        >
                            Simpan Perubahan
                        </button>
                        <a 
                            href="{{ route('admin.pricing.index') }}" 
                            class="px-6 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 font-medium"
                        >
                            Kembali
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
