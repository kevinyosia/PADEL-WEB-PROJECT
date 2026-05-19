<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Court;
use App\Models\Coach;
use App\Models\Equipment;
use App\Models\Membership;
use App\Models\PointHistory;
use App\Models\Reservation;
use App\Models\Transaction;
use Illuminate\Validation\ValidationException;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        // Time slots untuk picker (6:00 - 23:00)
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

        // Get all data
        $courts = Court::where('status', 'tersedia')->get();
        $coaches = Coach::where('availability_status', 'active')->get();
        $equipments = Equipment::where('kategori', 'sewa')->get();
        $products = Equipment::where('kategori', 'beli')->get();
        $membership = Auth::user()->membership;
        $isMember = $membership !== null;
        $availablePoints = $isMember ? (int) $membership->total_poin_aktif : 0;

        // Parse query parameters dari courts page
        $courtId = $request->query('court_id');
        $jamMulai = $request->query('jam_mulai', '08:00');
        $jamSelesai = $request->query('jam_selesai', '09:00');
        $tanggal = $request->query('tanggal_booking', now()->toDateString());

        $court = $courtId ? Court::find($courtId) : null;

        // Hitung durasi & harga
        $durasiJam = 1;
        $courtPrice = 0;
        $courtName = $court?->nama_lapangan ?? 'Bandeja Padel Arena';
        $tanggalFormatted = '';

        if ($court && $jamMulai && $jamSelesai) {
            $mulai = Carbon::createFromFormat('H:i', $jamMulai);
            $selesai = Carbon::createFromFormat('H:i', $jamSelesai);
            if ($selesai <= $mulai) {
                $selesai->addDay();
            }
            $durasiJam = max(1, $selesai->diffInHours($mulai));
            $courtPrice = ($mulai->hour >= 18 ? (int)$court->harga_malam : (int)$court->harga_pagi_tengahmalam) * $durasiJam;
        }

        if ($tanggal) {
            $dt = Carbon::parse($tanggal);
            $days = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
            $mons = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
            $tanggalFormatted = $days[$dt->dayOfWeek] . ', ' . $dt->day . ' ' . $mons[$dt->month - 1] . ' ' . $dt->year;
        }

        return view('user.booking.index', compact(
            'timeSlots', 'courts', 'coaches', 'equipments', 'products',
            'court', 'courtName', 'courtPrice', 'durasiJam', 'tanggalFormatted',
            'jamMulai', 'jamSelesai', 'tanggal', 'isMember', 'availablePoints'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'court_id' => ['required', 'exists:courts,id'],
            'coach_id' => ['nullable', 'exists:coaches,id'],
            'tanggal_booking' => ['required', 'date'],
            'jam_mulai' => ['required', 'date_format:H:i'],
            'jam_selesai' => ['required', 'date_format:H:i', 'after:jam_mulai'],
            'point_to_use' => ['nullable', 'integer', 'min:0'],
            'equipment_items' => ['nullable', 'array'],
            'equipment_items.*.equipment_id' => ['required_with:equipment_items', 'exists:equipment,id'],
            'equipment_items.*.jumlah' => ['required_with:equipment_items', 'integer', 'min:1'],
            'product_items' => ['nullable', 'array'],
            'product_items.*.product_id' => ['required_with:product_items', 'exists:equipment,id'],
            'product_items.*.jumlah' => ['required_with:product_items', 'integer', 'min:1'],
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
        $productItems = $validated['product_items'] ?? [];
        $pivotRows = [];
        $totalHargaPerlengkapan = 0;
        $totalHargaProduk = 0;

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

        foreach ($productItems as $item) {
            $product = Equipment::findOrFail($item['product_id']);
            $jumlah = (int) $item['jumlah'];
            $subtotal = (int) $product->harga * $jumlah;

            $pivotRows[$product->id] = [
                'jumlah_sewa' => $jumlah,
                'subtotal_harga' => $subtotal,
            ];

            $totalHargaProduk += $subtotal;
        }

        $grandTotal = $totalHargaLapangan + $totalHargaCoach + $totalHargaPerlengkapan + $totalHargaProduk;
        $pointToUse = (int) ($validated['point_to_use'] ?? 0);

        if ($pointToUse > 0 && !Auth::user()->membership) {
            throw ValidationException::withMessages([
                'point_to_use' => 'Hanya member yang dapat menggunakan poin.',
            ]);
        }

        /** @var Transaction $transaction */
        $transaction = DB::transaction(function () use ($validated, $pivotRows, $totalHargaLapangan, $totalHargaCoach, $totalHargaPerlengkapan, $totalHargaProduk, $grandTotal, $pointToUse) {
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

            $pointDiscount = 0;
            $membership = null;
            if ($pointToUse > 0) {
                $membership = Membership::query()
                    ->where('user_id', Auth::id())
                    ->lockForUpdate()
                    ->first();

                if (!$membership) {
                    throw ValidationException::withMessages([
                        'point_to_use' => 'Membership tidak ditemukan.',
                    ]);
                }

                $availablePoints = (int) $membership->total_poin_aktif;
                if ($pointToUse > $availablePoints) {
                    throw ValidationException::withMessages([
                        'point_to_use' => 'Poin tidak mencukupi. Sisa poin Anda: ' . number_format($availablePoints, 0, ',', '.'),
                    ]);
                }

                $pointDiscount = min($pointToUse, $grandTotal);
            }

            $finalGrandTotal = max(0, $grandTotal - $pointDiscount);

            $transaction = Transaction::create([
                'reservation_id' => $reservation->id,
                'total_harga_lapangan' => $totalHargaLapangan,
                'total_harga_coach' => $totalHargaCoach,
                'total_harga_perlengkapan' => $totalHargaPerlengkapan + $totalHargaProduk,
                'potongan_poin' => $pointDiscount,
                'grand_total' => $finalGrandTotal,
                'metode_pembayaran' => 'transfer',
                'channel_pembayaran' => 'virtual_account',
                'status_pembayaran' => 'belum_lunas',
            ]);

            if ($pointDiscount > 0 && $membership) {
                $membership->decrement('total_poin_aktif', $pointDiscount);
                $membership->increment('total_poin_terpakai', $pointDiscount);

                PointHistory::create([
                    'user_id' => Auth::id(),
                    'jumlah_poin' => -$pointDiscount,
                    'keterangan' => 'Penukaran poin untuk transaksi #' . $transaction->id,
                ]);
            }

            return $transaction;
        });

        // Redirect ke payment page
        return redirect()->route('payment.page', $transaction->id);
    }
}