<?php

namespace App\Http\Controllers;

use App\Models\Court;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CourtController extends Controller
{
    public function index()
    {
        // Akan mengarahkan ke file views/user/courts/index.blade.php
        return view('user.courts.index');
    }

    public function availability(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'date' => ['nullable', 'date'],
        ]);

        $date = Carbon::parse($validated['date'] ?? now())->toDateString();

        $courts = Court::query()
            ->orderBy('id')
            ->get(['id', 'nama_lapangan', 'status', 'harga_pagi_tengahmalam', 'harga_malam']);

        $reservations = Reservation::query()
            ->whereDate('tanggal_booking', $date)
            ->whereIn('status_reservasi', ['confirmed', 'completed'])
            ->get(['court_id', 'jam_mulai', 'jam_selesai']);

        $slotTemplate = [];
        for ($hour = 6; $hour <= 23; $hour++) {
            $slotTemplate[] = [
                'start' => sprintf('%02d:00', $hour),
                'end' => sprintf('%02d:00', ($hour + 1) % 24),
                'hour' => $hour,
            ];
        }

        $payload = $courts->map(function (Court $court) use ($reservations, $slotTemplate) {
            $courtStatusRaw = strtolower((string) $court->status);
            $isMaintenance = $courtStatusRaw !== 'tersedia';

            $slots = collect($slotTemplate)->map(function (array $slot) use ($court, $reservations, $isMaintenance) {
                $slotStart = $this->toMinutes($slot['start']);
                $slotEnd = $this->toMinutes($slot['end']);
                if ($slotEnd === 0) {
                    $slotEnd = 24 * 60;
                }

                $status = 'available';
                if ($isMaintenance) {
                    $status = 'maintenance';
                } else {
                    $isBooked = $reservations
                        ->where('court_id', $court->id)
                        ->contains(function (Reservation $reservation) use ($slotStart, $slotEnd) {
                            $bookingStart = $this->toMinutes($reservation->jam_mulai);
                            $bookingEnd = $this->toMinutes($reservation->jam_selesai);
                            if ($bookingEnd === 0) {
                                $bookingEnd = 24 * 60;
                            }

                            return $slotStart < $bookingEnd && $slotEnd > $bookingStart;
                        });

                    if ($isBooked) {
                        $status = 'booked';
                    }
                }

                $price = $slot['hour'] >= 18
                    ? (int) $court->harga_malam
                    : (int) $court->harga_pagi_tengahmalam;

                return [
                    'start' => $slot['start'],
                    'end' => $slot['end'],
                    'status' => $status,
                    'price' => $price,
                ];
            })->values();

            return [
                'id' => $court->id,
                'nama_lapangan' => $court->nama_lapangan,
                'status_lapangan' => $isMaintenance ? 'maintenance' : 'available',
                'slots' => $slots,
            ];
        })->values();

        return response()->json([
            'date' => $date,
            'slot_duration_minutes' => 60,
            'courts' => $payload,
        ]);
    }

    private function toMinutes(string $time): int
    {
        [$hours, $minutes] = array_map('intval', explode(':', substr($time, 0, 5)));

        return ($hours * 60) + $minutes;
    }
}