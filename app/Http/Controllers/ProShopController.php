<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use Illuminate\View\View;

class ProShopController extends Controller
{
    public function index(): View
    {
        $equipments = Equipment::where('kategori', 'beli')->get();
        
        return view('user.proshop.index', compact('equipments'));
    }
}
