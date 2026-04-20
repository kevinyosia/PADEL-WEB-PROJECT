<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCoachRequest;
use App\Http\Requests\UpdateCoachRequest;
use App\Models\Coach;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminCoachController extends Controller
{
    /**
     * Show coach management list
     */
    public function index()
    {
        $coaches = Coach::with('user')->get();
        
        $stats = [
            'total' => $coaches->count(),
            'active' => $coaches->where('availability_status', 'active')->count(),
            'inactive' => $coaches->where('availability_status', 'inactive')->count(),
            'on_leave' => $coaches->where('availability_status', 'on_leave')->count(),
        ];

        return view('admin.coaches.index', [
            'coaches' => $coaches,
            'stats' => $stats,
        ]);
    }

    /**
     * Show register coach form
     */
    public function create()
    {
        return view('admin.coaches.create');
    }

    /**
     * Store new coach (create user + coach record)
     */
    public function store(StoreCoachRequest $request)
    {
        $validated = $request->validated();

        // Create user account (admin-controlled)
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => Hash::make('password123'), // Default password
            'role' => 'coach',
        ]);

        // Create coach record
        Coach::create([
            'user_id' => $user->id,
            'deskripsi_keahlian' => $validated['deskripsi_keahlian'],
            'harga_per_jam' => $validated['harga_per_jam'],
            'availability_status' => $validated['availability_status'],
            'schedule' => [
                'mon' => $validated['schedule']['mon'] ?? false,
                'tue' => $validated['schedule']['tue'] ?? false,
                'wed' => $validated['schedule']['wed'] ?? false,
                'thu' => $validated['schedule']['thu'] ?? false,
                'fri' => $validated['schedule']['fri'] ?? false,
            ],
        ]);

        return redirect()->route('admin.coaches.index')->with('success', 'Coach berhasil didaftarkan. Password default: password123');
    }

    /**
     * Show edit coach form
     */
    public function edit(Coach $coach)
    {
        return view('admin.coaches.edit', compact('coach'));
    }

    /**
     * Update coach data
     */
    public function update(UpdateCoachRequest $request, Coach $coach)
    {
        $validated = $request->validated();

        $coach->update([
            'deskripsi_keahlian' => $validated['deskripsi_keahlian'],
            'harga_per_jam' => $validated['harga_per_jam'],
            'availability_status' => $validated['availability_status'],
            'schedule' => [
                'mon' => $validated['schedule']['mon'] ?? false,
                'tue' => $validated['schedule']['tue'] ?? false,
                'wed' => $validated['schedule']['wed'] ?? false,
                'thu' => $validated['schedule']['thu'] ?? false,
                'fri' => $validated['schedule']['fri'] ?? false,
            ],
        ]);

        return redirect()->route('admin.coaches.index')->with('success', 'Data coach berhasil diperbarui');
    }

    /**
     * Quick update availability status (via dropdown/inline)
     */
    public function updateAvailability(Coach $coach)
    {
        $coach->update([
            'availability_status' => request()->input('availability_status'),
        ]);

        return redirect()->back()->with('success', 'Status ketersediaan berhasil diperbarui');
    }

    /**
     * Delete coach
     */
    public function destroy(Coach $coach)
    {
        $user = $coach->user;
        $coach->delete();
        $user->delete();

        return redirect()->route('admin.coaches.index')->with('success', 'Coach dan akunnya berhasil dihapus');
    }
}
