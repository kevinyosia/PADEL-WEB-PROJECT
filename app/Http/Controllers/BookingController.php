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
use App\Models\PointHistory;

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
            'equipment_items' => ['nullable', 'array'],
            'equipment_items.*.equipment_id' => ['required_with:equipment_items', 'exists:equipment,id'],
            'equipment_items.*.jumlah' => ['required_with:equipment_items', 'integer', 'min:1'],
        ]);

        $user = Auth::user();
        $isMember = $user->membership()->exists();
        $holdHours = $isMember ? 48 : 8;

        $mulai = Carbon::createFromFormat('Y-m-d H:i', $validated['tanggal_booking'].' '.$validated['jam_mulai']);
        $selesai = Carbon::createFromFormat('Y-m-d H:i', $validated['tanggal_booking'].' '.$validated['jam_selesai']);
        $durasiJam = max(1, $selesai->diffInHours($mulai));

        $court = Court::findOrFail($validated['court_id']);
        $coach = !empty($validated['coach_id']) ? Coach::findOrFail($validated['coach_id']) : null;

        $isWeekend = in_array($mulai->dayOfWeek, [Carbon::SATURDAY, Carbon::SUNDAY], true);
        if ($isWeekend) {
            $hargaLapanganPerJam = $court->harga_weekend;
        } elseif ($mulai->hour >= 18) {
            $hargaLapanganPerJam = $court->harga_malam;
        } else {
            $hargaLapanganPerJam = $court->harga_pagi_tengahmalam;
        }

        $totalHargaLapangan = $hargaLapanganPerJam * $durasiJam;
        $totalHargaCoach = $coach ? ($coach->harga_per_jam * $durasiJam) : 0;

        $equipmentItems = $validated['equipment_items'] ?? [];
        $pivotRows = [];
        $totalHargaPerlengkapan = 0;

        foreach ($equipmentItems as $item) {
            $equipment = Equipment::findOrFail($item['equipment_id']);
            $jumlah = (int) $item['jumlah'];
            $subtotal = $equipment->harga * $jumlah;

            $pivotRows[$equipment->id] = [
                'jumlah_sewa' => $jumlah,
                'subtotal_harga' => $subtotal,
            ];

            $totalHargaPerlengkapan += $subtotal;
        }

        $grandTotal = $totalHargaLapangan + $totalHargaCoach + $totalHargaPerlengkapan;

        DB::transaction(function () use (
            $user,
            $validated,
            $pivotRows,
            $totalHargaLapangan,
            $totalHargaCoach,
            $totalHargaPerlengkapan,
            $grandTotal,
            $holdHours
        ) {
            $reservation = Reservation::create([
                'user_id' => $user->id,
                'court_id' => $validated['court_id'],
                'coach_id' => $validated['coach_id'] ?? null,
                'tanggal_booking' => $validated['tanggal_booking'],
                'jam_mulai' => $validated['jam_mulai'],
                'jam_selesai' => $validated['jam_selesai'],
                'status_reservasi' => 'pending',
                'batas_pembayaran' => now()->addHours($holdHours),
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
                'channel_pembayaran' => null,
                'status_pembayaran' => 'belum_lunas',
            ]);
        });

        return redirect()->route('booking.index')->with('status', 'Reservasi berhasil dibuat. Silakan lanjutkan pembayaran transfer.');
    }

    public function pay(Request $request, Reservation $reservation)
    {
        if ((int) $reservation->user_id !== (int) Auth::id()) {
            abort(403);
        }

        if ($reservation->status_reservasi === 'cancelled') {
            return back()->withErrors(['reservation' => 'Reservasi sudah dibatalkan.']);
        }

        if ($reservation->batas_pembayaran && now()->greaterThan($reservation->batas_pembayaran)) {
            $reservation->update(['status_reservasi' => 'cancelled']);
            $reservation->transaction()?->update(['status_pembayaran' => 'belum_lunas']);

            return back()->withErrors(['reservation' => 'Batas waktu pembayaran sudah lewat, reservasi dibatalkan otomatis.']);
        }

        $validated = $request->validate([
            'payment_channel' => ['required', 'in:virtual_account,m_banking'],
            'use_points' => ['nullable', 'integer', 'min:0'],
        ]);

        $transaction = $reservation->transaction;
        if (!$transaction) {
            return back()->withErrors(['payment' => 'Transaksi reservasi tidak ditemukan.']);
        }

        $user = Auth::user();
        $membership = $user->membership;
        $isMember = (bool) $membership;
        $pointsToUse = (int) ($validated['use_points'] ?? 0);

        DB::transaction(function () use ($reservation, $transaction, $validated, $isMember, $membership, $pointsToUse) {
            if ($pointsToUse > 0) {
                if (!$isMember) {
                    abort(422, 'Penukaran poin hanya untuk member.');
                }

                if ($membership->total_poin_aktif < $pointsToUse) {
                    abort(422, 'Poin aktif tidak mencukupi.');
                }

                $membership->decrement('total_poin_aktif', $pointsToUse);
                $membership->increment('total_poin_terpakai', $pointsToUse);

                PointHistory::create([
                    'user_id' => $reservation->user_id,
                    'jumlah_poin' => -1 * $pointsToUse,
                    'keterangan' => 'Pengeluaran poin untuk reservasi #'.$reservation->id,
                ]);

                $transaction->grand_total = max(0, (int) $transaction->grand_total - $pointsToUse);
            }

            $transaction->metode_pembayaran = 'transfer';
            $transaction->channel_pembayaran = $validated['payment_channel'];
            $transaction->status_pembayaran = 'lunas';

            if ($isMember) {
                $rentingEquipmentSubtotal = (int) $reservation->equipment()
                    ->where('equipment.kategori', 'sewa')
                    ->sum('reservation_equipment.subtotal_harga');

                $buyingEquipmentSubtotal = (int) $reservation->equipment()
                    ->where('equipment.kategori', 'beli')
                    ->sum('reservation_equipment.subtotal_harga');

                $cashbackSewa = (int) floor((($transaction->total_harga_lapangan + $rentingEquipmentSubtotal) * 8) / 100);
                $cashbackBeli = (int) floor(($buyingEquipmentSubtotal * 5) / 100);
                $cashbackTotal = $cashbackSewa + $cashbackBeli;

                if ($cashbackTotal > 0) {
                    $membership->increment('total_poin_aktif', $cashbackTotal);

                    PointHistory::create([
                        'user_id' => $reservation->user_id,
                        'jumlah_poin' => $cashbackTotal,
                        'keterangan' => 'Pemasukan cashback poin reservasi #'.$reservation->id,
                    ]);
                }
            }

            $transaction->save();
            $reservation->update(['status_reservasi' => 'confirmed']);
        });

        return redirect()->route('booking.index')->with('status', 'Pembayaran transfer berhasil diproses.');
    }
}