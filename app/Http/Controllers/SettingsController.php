<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use App\Models\Reservation;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class SettingsController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        $reservations = Reservation::with(['court', 'transaction', 'equipment'])
            ->where('user_id', $user->id)
            ->whereHas('transaction', function ($query) {
                $query->where('status_pembayaran', 'lunas');
            })
            ->orderBy('tanggal_booking', 'desc')
            ->get();

        return view('user.settings.index', compact('user', 'reservations'));
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('password_updated', true);
    }
}
