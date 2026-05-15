<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateConsumablePriceRequest;
use App\Http\Requests\UpdateEquipmentRateRequest;
use App\Http\Requests\UpdateEquipmentStockRequest;
use App\Http\Requests\UpdateConsumableStockRequest;
use App\Http\Requests\StoreEquipmentRequest;
use App\Http\Requests\StoreConsumableRequest;
use App\Models\Equipment;

class AdminInventoryController extends Controller
{
    /**
     * Show inventory & rentals overview
     */
    public function index()
    {
        // Get all sales items (kategori='beli') merged from Equipment table
        $salesItems = Equipment::where('kategori', 'beli')->get();
        
        // Get all rental items (kategori='sewa')
        $rentalItems = Equipment::where('kategori', 'sewa')->get();

        return view('admin.inventory.index', [
            'salesItems' => $salesItems,
            'rentalItems' => $rentalItems,
        ]);
    }

    /**
     * Update equipment sales price (formerly consumable price)
     */
    public function updateEquipmentPrice(UpdateConsumablePriceRequest $request, Equipment $equipment)
    {
        $validated = $request->validated();

        $equipment->update([
            'harga' => $validated['purchase_price'],
        ]);

        return redirect()->back()->with('success', 'Harga barang berhasil diperbarui');
    }

    /**
     * Update equipment/rental item rate
     */
    public function updateEquipmentRate(UpdateEquipmentRateRequest $request, Equipment $equipment)
    {
        $validated = $request->validated();

        $equipment->update([
            'rental_rate' => $validated['rental_rate'],
        ]);

        return redirect()->back()->with('success', 'Harga rental berhasil diperbarui');
    }

    /**
     * Update equipment stock (for both rental and sales)
     */
    public function updateEquipmentStock(UpdateEquipmentStockRequest $request, Equipment $equipment)
    {
        $validated = $request->validated();

        $equipment->update([
            'stock_quantity' => $validated['stock_quantity'],
        ]);

        $itemType = $equipment->kategori === 'sewa' ? 'rental' : 'penjualan';
        return redirect()->back()->with('success', "Stok barang {$itemType} berhasil diperbarui");
    }

    /**
     * Store new equipment (rental or sales)
     */
    public function storeEquipment(StoreEquipmentRequest $request)
    {
        $validated = $request->validated();

        Equipment::create($validated);

        $itemType = $validated['kategori'] === 'sewa' ? 'rental' : 'penjualan';
        return redirect()->back()->with('success', "Barang {$itemType} berhasil ditambahkan");
    }

    /**
     * Legacy: Redirect to updateEquipmentRate
     * Kept for backward compatibility
     */
    public function updateRentalItemRate(UpdateEquipmentRateRequest $request, Equipment $equipment)
    {
        return $this->updateEquipmentRate($request, $equipment);
    }

    /**
     * Legacy: Redirect to updateEquipmentPrice
     * Kept for backward compatibility
     */
    public function updateConsumablePrice(UpdateConsumablePriceRequest $request, Equipment $equipment)
    {
        return $this->updateEquipmentPrice($request, $equipment);
    }

    /**
     * Legacy: Redirect to updateEquipmentStock
     * Kept for backward compatibility
     */
    public function updateConsumableStock(UpdateEquipmentStockRequest $request, Equipment $equipment)
    {
        return $this->updateEquipmentStock($request, $equipment);
    }

    /**
     * Legacy: Redirect to storeEquipment
     * Kept for backward compatibility
     */
    public function storeConsumable(StoreEquipmentRequest $request)
    {
        return $this->storeEquipment($request);
    }
}

