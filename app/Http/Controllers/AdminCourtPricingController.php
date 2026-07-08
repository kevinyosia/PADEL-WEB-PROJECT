<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdminCourtPricingRequest;
use App\Models\Court;
use Illuminate\Http\Request;

class AdminCourtPricingController extends Controller
{
    /**
     * Show admin pricing form for a specific court
     */
    public function edit(Court $court)
    {
        return view('admin.court-pricing.edit', compact('court'));
    }

    /**
     * Update court pricing
     */
    public function update(AdminCourtPricingRequest $request, Court $court)
    {
        $validated = $request->validated();

        $court->update([
            'harga_pagi_tengahmalam' => $validated['harga_pagi_tengahmalam'],
            'harga_malam' => $validated['harga_malam'],
            'harga_weekend' => $validated['harga_weekend'],
            'harga_weekend_prime' => $validated['harga_weekend_prime'],
        ]);

        return redirect()->back()->with('success', 'Harga lapangan berhasil diperbarui');
    }

    /**
     * Show pricing list for all courts
     */
    public function index()
    {
        $courts = Court::all();
        return view('admin.court-pricing.index', compact('courts'));
    }
}
