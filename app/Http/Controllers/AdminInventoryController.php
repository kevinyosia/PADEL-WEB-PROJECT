<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateConsumablePriceRequest;
use App\Http\Requests\UpdateRentalItemRateRequest;
use App\Models\Consumable;
use App\Models\RentalItem;

class AdminInventoryController extends Controller
{
    /**
     * Show inventory & rentals overview
     */
    public function index()
    {
        $consumables = Consumable::all();
        $rentalItems = RentalItem::all();

        return view('admin.inventory.index', [
            'consumables' => $consumables,
            'rentalItems' => $rentalItems,
        ]);
    }

    /**
     * Update consumable purchase price
     */
    public function updateConsumablePrice(UpdateConsumablePriceRequest $request, Consumable $consumable)
    {
        $validated = $request->validated();

        $consumable->update([
            'purchase_price' => $validated['purchase_price'],
        ]);

        return redirect()->back()->with('success', 'Harga barang berhasil diperbarui');
    }

    /**
     * Update rental item rate
     */
    public function updateRentalItemRate(UpdateRentalItemRateRequest $request, RentalItem $rentalItem)
    {
        $validated = $request->validated();

        $rentalItem->update([
            'rental_rate' => $validated['rental_rate'],
        ]);

        return redirect()->back()->with('success', 'Harga rental berhasil diperbarui');
    }
}
