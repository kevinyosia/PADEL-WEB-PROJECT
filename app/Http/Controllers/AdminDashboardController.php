<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateCourtStatusRequest;
use App\Models\Court;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    /**
     * Show court management overview
     */
    public function courtManagement()
    {
        $courts = Court::all();
        
        $courtsWithBookings = $courts->map(function ($court) {
            // Get current booking (if any)
            $currentBooking = Reservation::where('court_id', $court->id)
                ->where('status_reservasi', 'confirmed')
                ->whereDate('tanggal_booking', Carbon::today())
                ->where('jam_selesai', '>', Carbon::now()->format('H:i'))
                ->where('jam_mulai', '<=', Carbon::now()->format('H:i'))
                ->first();

            // Get next booking
            $nextBooking = Reservation::where('court_id', $court->id)
                ->where('status_reservasi', 'confirmed')
                ->where(function ($query) {
                    $query->whereDate('tanggal_booking', '>', Carbon::today())
                        ->orWhere(function ($q) {
                            $q->whereDate('tanggal_booking', Carbon::today())
                                ->where('jam_mulai', '>', Carbon::now()->format('H:i'));
                        });
                })
                ->orderBy('tanggal_booking')
                ->orderBy('jam_mulai')
                ->first();

            return [
                'id' => $court->id,
                'nama_lapangan' => $court->nama_lapangan,
                'status' => strtoupper($court->status),
                'status_lower' => strtolower($court->status),
                'current_booking' => $currentBooking ? [
                    'user_name' => $currentBooking->user->name ?? 'Unknown',
                    'time' => $currentBooking->jam_mulai . ' - ' . $currentBooking->jam_selesai,
                    'duration_minutes' => $this->calculateDuration($currentBooking->jam_mulai, $currentBooking->jam_selesai),
                ] : null,
                'next_booking' => $nextBooking ? [
                    'user_name' => $nextBooking->user->name ?? 'Unknown',
                    'time' => $nextBooking->jam_mulai,
                    'date' => $nextBooking->tanggal_booking,
                ] : null,
                'no_active_bookings' => !$currentBooking,
                'description' => $court->deskripsi ?? 'Standard court',
            ];
        });

        return view('admin.dashboard.court-management', [
            'courts' => $courtsWithBookings,
        ]);
    }

    /**
     * Update court status
     */
    public function updateCourtStatus(Court $court, UpdateCourtStatusRequest $request)
    {
        $validated = $request->validated();

        $court->update([
            'status' => strtolower($validated['status']),
        ]);

        return redirect()->back()->with('success', 'Status lapangan berhasil diperbarui');
    }

    /**
     * Calculate duration in minutes from time strings
     */
    private function calculateDuration($startTime, $endTime)
    {
        $start = Carbon::createFromFormat('H:i', $startTime);
        $end = Carbon::createFromFormat('H:i', $endTime);
        
        if ($end < $start) {
            $end->addDay();
        }

        return $end->diffInMinutes($start);
    }
}
