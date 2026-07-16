<?php

namespace App\Http\Controllers;

use App\Models\Coach;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CoachesPageController extends Controller
{
    public function index(): View
    {
        $coaches = Coach::with(['user', 'reviews'])
            ->where('availability_status', '<>', 'deleted')
            ->get();

        return view('user.coaches.index', compact('coaches'));
    }

    /**
     * Return 1-hour slot availability for a coach on a specific day.
     *
     * Query params:
     *   - day  : mon|tue|wed|thu|fri
     *   - date : Y-m-d (optional, defaults to today)
     *
     * Response: JSON array of { start: "HH:MM", end: "HH:MM", available: bool }
     */
    public function slots(Coach $coach, Request $request): JsonResponse
    {
        $request->validate([
            'day' => ['required', 'in:mon,tue,wed,thu,fri'],
            'date' => ['nullable', 'date_format:Y-m-d'],
        ]);

        $day = $request->input('day');
        $date = $request->input('date', now()->toDateString());

        $sessions = $coach->getSessionsForDay($day);

        if (empty($sessions) || ! $coach->isAvailableOnDay($day)) {
            return response()->json(['slots' => []]);
        }

        // Build all 1-hour slots from all sessions of the day
        $allSlots = [];

        foreach ($sessions as $session) {
            $cursor = Carbon::createFromFormat('H:i', $session['start']);
            $endAt = Carbon::createFromFormat('H:i', $session['end']);

            while ($cursor->lt($endAt)) {
                $slotStart = $cursor->format('H:i');
                $cursor->addHour();
                $slotEnd = $cursor->format('H:i');

                $allSlots[] = ['start' => $slotStart, 'end' => $slotEnd];
            }
        }

        // Check which slots are already booked (confirmed / completed)
        $bookedSlots = Reservation::query()
            ->where('coach_id', $coach->id)
            ->whereDate('tanggal_booking', $date)
            ->whereIn('status_reservasi', ['confirmed', 'completed'])
            ->get(['jam_mulai', 'jam_selesai']);

        $result = array_map(function (array $slot) use ($bookedSlots) {
            $slotStart = $slot['start'];
            $slotEnd = $slot['end'];

            $isBooked = $bookedSlots->contains(function ($reservation) use ($slotStart, $slotEnd) {
                // A slot is booked if it overlaps any confirmed reservation
                return $reservation->jam_mulai < $slotEnd && $reservation->jam_selesai > $slotStart;
            });

            return [
                'start' => $slotStart,
                'end' => $slotEnd,
                'available' => ! $isBooked,
            ];
        }, $allSlots);

        return response()->json(['slots' => $result]);
    }
}
