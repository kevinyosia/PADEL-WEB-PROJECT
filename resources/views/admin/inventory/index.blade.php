@extends('layouts.admin')

@section('content')
<div style="max-width: 1280px; margin: 0 auto;">
    <!-- Header Section -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Inventory & Rentals</h1>
                <p class="text-gray-600 mt-1">MANAGEMENT HUB</p>
            </div>
            <button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                + New Asset
            </button>
        </div>

        @if(session('success'))
            <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-700 rounded">
                {{ session('success') }}
            </div>
        @endif

        <!-- Sales Inventory Section (Equipment kategori='beli') -->
        <div class="mb-12">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-900">Sales Inventory <span class="text-sm text-gray-500 font-normal">(Products for Purchase)</span></h2>
                <a href="#" class="text-blue-600 hover:text-blue-800 font-medium text-sm">Export CSV</a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($salesItems as $item)
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <!-- Status Badge -->
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                @php
                                    $status = $item->stock_quantity > 0 ? 'IN STOCK' : 'OUT OF STOCK';
                                    $statusClasses = [
                                        'IN STOCK' => 'bg-green-100 text-green-800',
                                        'OUT OF STOCK' => 'bg-red-100 text-red-800',
                                    ];
                                    $statusClass = $statusClasses[$status] ?? 'bg-gray-100 text-gray-800';
                                @endphp
                                <span class="inline-block px-3 py-1 rounded-full text-xs font-bold {{ $statusClass }}">
                                    • {{ $status }}
                                </span>
                            </div>
                            <div class="text-3xl">🏸</div>
                        </div>

                        <!-- Product Info -->
                        <h3 class="text-lg font-bold text-gray-900">{{ $item->nama_alat }}</h3>
                        <p class="text-xs text-gray-500 mb-4">SKU: {{ $item->sku ?? 'N/A' }}</p>

                        <!-- Price Section -->
                        <form action="{{ route('admin.equipment.update-price', $item) }}" method="POST" class="mb-4">
                            @csrf
                            @method('PATCH')
                            <div class="flex items-center mb-2">
                                <label class="text-sm text-gray-600 font-medium mr-2">Price</label>
                                <div class="flex items-center">
                                    <span class="text-gray-600">Rp</span>
                                    <input 
                                        type="number" 
                                        name="purchase_price" 
                                        value="{{ $item->harga }}"
                                        class="w-28 px-2 py-1 border border-gray-300 rounded text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    >
                                    <button type="submit" class="ml-2 px-3 py-1 text-blue-600 hover:text-blue-800" title="Edit price">
                                        ✏️
                                    </button>
                                </div>
                            </div>
                            @error('purchase_price')
                                <p class="text-red-500 text-xs">{{ $message }}</p>
                            @enderror
                        </form>

                        <!-- Stock Section -->
                        <form action="{{ route('admin.equipment.update-stock', $item) }}" method="POST" class="mb-4">
                            @csrf
                            @method('PATCH')
                            <div class="flex items-center">
                                <label class="text-sm text-gray-600 font-medium mr-2">Stock</label>
                                <div class="flex items-center">
                                    <input 
                                        type="number" 
                                        name="stock_quantity" 
                                        value="{{ $item->stock_quantity }}"
                                        class="w-20 px-2 py-1 border border-gray-300 rounded text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        min="0"
                                    >
                                    <button type="submit" class="ml-2 px-3 py-1 text-blue-600 hover:text-blue-800" title="Edit stock">
                                        ✏️
                                    </button>
                                </div>
                            </div>
                            @error('stock_quantity')
                                <p class="text-red-500 text-xs">{{ $message }}</p>
                            @enderror
                        </form>

                        <!-- Current Stock Display -->
                        <div class="text-sm">
                            <p class="text-gray-600">Current Stock</p>
                            <p class="text-lg font-bold text-gray-900">{{ $item->stock_quantity }}/{{ $item->max_capacity }}</p>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full bg-white rounded-lg shadow-md p-12 text-center">
                        <p class="text-gray-500">Tidak ada barang penjualan yang ditemukan</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Rental Items Section -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Rental Items <span class="text-sm text-gray-500 font-normal">(Hardware)</span></h2>

            @if($rentalItems->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900">ITEM DETAILS</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900">CATEGORY</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900">RENTAL RATE</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900">UTILIZATION</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900">INVENTORY</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($rentalItems as $item)
                                <tr class="hover:bg-gray-50">
                                    <!-- Item Details -->
                                    <td class="px-4 py-4">
                                        <div class="flex items-center">
                                            <div class="text-3xl mr-3">🎾</div>
                                            <div>
                                                <p class="font-semibold text-gray-900">{{ $item->name }}</p>
                                                <p class="text-xs text-gray-500">{{ $item->sku }}</p>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Category -->
                                    <td class="px-4 py-4">
                                        <p class="text-sm text-gray-700">{{ $item->category }}</p>
                                    </td>

                                    <!-- Rental Rate -->
                                    <td class="px-4 py-4">
                                        <form action="{{ route('admin.equipment.update-rate', $item) }}" method="POST" class="flex items-center gap-2">
                                            @csrf
                                            @method('PATCH')
                                            <span class="text-gray-600">$</span>
                                            <input 
                                                type="number" 
                                                name="rental_rate" 
                                                value="{{ $item->rental_rate }}"
                                                class="w-20 px-2 py-1 border border-gray-300 rounded text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                            >
                                            <button type="submit" class="text-blue-600 hover:text-blue-800" title="Edit rate">
                                                ✏️
                                            </button>
                                        </form>
                                        @error('rental_rate')
                                            <p class="text-red-500 text-xs">{{ $message }}</p>
                                        @enderror
                                    </td>

                                    <!-- Utilization -->
                                    <td class="px-4 py-4">
                                        @php
                                            $utilization = $item->getUtilizationPercentage();
                                        @endphp
                                        <div class="flex items-center gap-2">
                                            <div class="w-24 bg-gray-200 rounded-full h-2">
                                                <div 
                                                    class="bg-blue-600 h-2 rounded-full" 
                                                    style="width: {{ $utilization }}%"
                                                ></div>
                                            </div>
                                            <span class="text-xs font-medium text-gray-700">{{ $utilization }}%</span>
                                        </div>
                                        <p class="text-xs text-gray-500 mt-1">{{ $item->getStockStatus() }}</p>
                                    </td>

                                    <!-- Inventory -->
                                    <td class="px-4 py-4">
                                        <form action="{{ route('admin.equipment.update-stock', $item) }}" method="POST" class="flex items-center gap-2 mb-2">
                                            @csrf
                                            @method('PATCH')
                                            <input 
                                                type="number" 
                                                name="stock_quantity" 
                                                value="{{ $item->stock_quantity }}"
                                                class="w-16 px-2 py-1 border border-gray-300 rounded text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                                min="0"
                                            >
                                            <span class="text-gray-600">/</span>
                                            <span class="text-sm font-semibold text-gray-900">{{ $item->max_capacity }}</span>
                                            <button type="submit" class="text-blue-600 hover:text-blue-800" title="Edit stock">
                                                ✏️
                                            </button>
                                        </form>
                                        @error('stock_quantity')
                                            <p class="text-red-500 text-xs">{{ $message }}</p>
                                        @enderror
                                        <p class="text-xs text-gray-500">{{ $item->getStockStatus() }}</p>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-12">
                    <p class="text-gray-500">Tidak ada barang rental yang ditemukan</p>
                </div>
            @endif
        </div>
    </div>
@endsection
