<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use Illuminate\View\View;

class ProShopController extends Controller
{
    public function index(): View
    {
        // Get all sales items (kategori='beli') with stock status
        $equipments = Equipment::where('kategori', 'beli')
            ->get()
            ->map(function ($item) {
                $item->stock_status = $item->stock_quantity > 0 ? 'in_stock' : 'sold_out';
                return $item;
            });
        
        return view('user.proshop.index', compact('equipments'));
    }
}
