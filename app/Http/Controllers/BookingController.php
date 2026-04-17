<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Court;
use App\Models\Coach;
use App\Models\Equipment;
use App\Models\Reservation;
use App\Models\Transaction;
use Illuminate\Validation\ValidationException;

class BookingController extends Controller
{
    public function index()
    {
        
        $timeSlots = [];
        $start = Carbon::createFromTime(6, 0); 
        $end = Carbon::createFromTime(0, 0)->addDay();

        while ($start < $end) {
            $timeSlots[] = $start->format('H:i');
            $start->addHour();
        }

        
        if (!in_array('00:00', $timeSlots) && !in_array('24:00', $timeSlots)) {
            $timeSlots[] = '00:00';
        }

        
        $courts = Court::where('status', 'tersedia')->get();
        
        
        $coaches = Coach::all();
        
       
        $equipments = Equipment::all();

        
        return view('booking.index', compact('timeSlots', 'courts', 'coaches', 'equipments'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'court_id' => ['required', 'exists:courts,id'],
            'coach_id' => ['nullable', 'exists:coaches,id'],
            'tanggal_booking' => ['required', 'date'],
            'jam_mulai' => ['required', 'date_format:H:i'],
            'jam_selesai' => ['required', 'date_format:H:i', 'after:jam_mulai'],
            'payment_channel' => ['required', 'in:virtual_account,m_banking'],
            'equipment_items' => ['nullable', 'array'],
            'equipment_items.*.equipment_id' => ['required_with:equipment_items', 'exists:equipment,id'],
            'equipment_items.*.jumlah' => ['required_with:equipment_items', 'integer', 'min:1'],
        ]);

        $mulai = Carbon::createFromFormat('Y-m-d H:i', $validated['tanggal_booking'].' '.$validated['jam_mulai']);
        $selesai = Carbon::createFromFormat('Y-m-d H:i', $validated['tanggal_booking'].' '.$validated['jam_selesai']);
        $durasiJam = max(1, $selesai->diffInHours($mulai));

        $court = Court::findOrFail($validated['court_id']);
        $coach = !empty($validated['coach_id']) ? Coach::findOrFail($validated['coach_id']) : null;

        $hargaLapanganPerJam = $mulai->hour >= 18
            ? (int) $court->harga_malam
            : (int) $court->harga_pagi_tengahmalam;

        $totalHargaLapangan = $hargaLapanganPerJam * $durasiJam;
        $totalHargaCoach = $coach ? ((int) $coach->harga_per_jam * $durasiJam) : 0;

        $equipmentItems = $validated['equipment_items'] ?? [];
        $pivotRows = [];
        $totalHargaPerlengkapan = 0;

        foreach ($equipmentItems as $item) {
            $equipment = Equipment::findOrFail($item['equipment_id']);
            $jumlah = (int) $item['jumlah'];
            $subtotal = (int) $equipment->harga * $jumlah;

            $pivotRows[$equipment->id] = [
                'jumlah_sewa' => $jumlah,
                'subtotal_harga' => $subtotal,
            ];

            $totalHargaPerlengkapan += $subtotal;
        }

        $grandTotal = $totalHargaLapangan + $totalHargaCoach + $totalHargaPerlengkapan;

        DB::transaction(function () use ($validated, $pivotRows, $totalHargaLapangan, $totalHargaCoach, $totalHargaPerlengkapan, $grandTotal) {
            $isTaken = Reservation::query()
                ->where('court_id', $validated['court_id'])
                ->whereDate('tanggal_booking', $validated['tanggal_booking'])
                ->whereIn('status_reservasi', ['confirmed', 'completed'])
                ->where('jam_mulai', '<', $validated['jam_selesai'])
                ->where('jam_selesai', '>', $validated['jam_mulai'])
                ->lockForUpdate()
                ->exists();

            if ($isTaken) {
                throw ValidationException::withMessages([
                    'jam_mulai' => 'Slot sudah terisi. Silakan pilih jam lain.',
                ]);
            }

            $reservation = Reservation::create([
                'user_id' => Auth::id(),
                'court_id' => $validated['court_id'],
                'coach_id' => $validated['coach_id'] ?? null,
                'tanggal_booking' => $validated['tanggal_booking'],
                'jam_mulai' => $validated['jam_mulai'],
                'jam_selesai' => $validated['jam_selesai'],
                'status_reservasi' => 'confirmed',
                'batas_pembayaran' => null,
            ]);

            if (!empty($pivotRows)) {
                $reservation->equipment()->attach($pivotRows);
            }

            Transaction::create([
                'reservation_id' => $reservation->id,
                'total_harga_lapangan' => $totalHargaLapangan,
                'total_harga_coach' => $totalHargaCoach,
                'total_harga_perlengkapan' => $totalHargaPerlengkapan,
                'grand_total' => $grandTotal,
                'metode_pembayaran' => 'transfer',
                'channel_pembayaran' => $validated['payment_channel'],
                'status_pembayaran' => 'lunas',
            ]);
        });

        return redirect()->route('booking.index')->with('status', 'Reservasi dan pembayaran berhasil diproses.');
    }
}