<?php

namespace App\Http\Controllers;

use App\Models\Coach;
use App\Models\Court;
use App\Models\Equipment;
use App\Models\Membership;
use App\Models\PointHistory;
use App\Models\Reservation;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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

        if (! in_array('00:00', $timeSlots) && ! in_array('24:00', $timeSlots)) {
            $timeSlots[] = '00:00';
        }

        // Parse query parameters dari courts page
        $courtId = $request->query('court_id');
        $jamMulai = $request->query('jam_mulai', '08:00');
        $jamSelesai = $request->query('jam_selesai', '09:00');
        $tanggal = $request->query('tanggal_booking', now()->toDateString());

        // Get all data
        $courts = Court::where('status', 'tersedia')->get();

        // Map PHP dayOfWeek (0=Sun … 6=Sat) to schedule key
        $dayMap = [0 => 'sun', 1 => 'mon', 2 => 'tue', 3 => 'wed', 4 => 'thu', 5 => 'fri', 6 => 'sat'];
        $bookingDay = $tanggal ? $dayMap[Carbon::parse($tanggal)->dayOfWeek] ?? null : null;

        // Only show active coaches who have sessions on the booking day
        $coaches = Coach::where('availability_status', 'active')
            ->with('user')
            ->get()
            ->filter(function (Coach $coach) use ($bookingDay) {
                if (! $bookingDay) {
                    return true;
                }

                return $coach->isAvailableOnDay($bookingDay)
                    && ! empty($coach->getSessionsForDay($bookingDay));
            })
            ->values();
        $equipments = Equipment::where('kategori', 'sewa')->get();
        $products = Equipment::where('kategori', 'beli')->get();
        $membership = Auth::user()->membership;
        $isMember = $membership !== null;
        $availablePoints = $isMember ? (int) $membership->total_poin_aktif : 0;

        $court = $courtId ? Court::find($courtId) : null;

        // Hitung durasi & harga
        $durasiJam = 1;
        $courtPrice = 0;
        $courtName = $court?->nama_lapangan ?? 'Bandeja Padel Arena';
        $tanggalFormatted = '';

        if ($court && $jamMulai && $jamSelesai) {
            $mulai = Carbon::createFromFormat('Y-m-d H:i', $tanggal.' '.$jamMulai);
            $selesai = Carbon::createFromFormat('Y-m-d H:i', $tanggal.' '.$jamSelesai);
            if ($selesai <= $mulai) {
                $selesai->addDay();
            }
            $durasiJam = max(1, $selesai->diffInHours($mulai));
            $courtPrice = $court->priceForRange($mulai, $selesai);
        }

        if ($tanggal) {
            $dt = Carbon::parse($tanggal);
            $days = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
            $mons = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
            $tanggalFormatted = $days[$dt->dayOfWeek].', '.$dt->day.' '.$mons[$dt->month - 1].' '.$dt->year;
        }

        return view('user.booking.index', compact(
            'timeSlots', 'courts', 'coaches', 'equipments', 'products',
            'court', 'courtName', 'courtPrice', 'durasiJam', 'tanggalFormatted',
            'jamMulai', 'jamSelesai', 'tanggal', 'isMember', 'availablePoints', 'bookingDay'
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
            'coach_slot_start' => ['nullable', 'date_format:H:i'],
            'coach_slot_end' => ['nullable', 'date_format:H:i'],
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
        $coach = ! empty($validated['coach_id']) ? Coach::findOrFail($validated['coach_id']) : null;

        // Validate that the selected coach slot belongs to the coach's sessions for that day
        if ($coach) {
            $dayMap = [0 => 'sun', 1 => 'mon', 2 => 'tue', 3 => 'wed', 4 => 'thu', 5 => 'fri', 6 => 'sat'];
            $bookingDay = $dayMap[Carbon::parse($validated['tanggal_booking'])->dayOfWeek];

            $slotStart = $validated['coach_slot_start'] ?? null;
            $slotEnd = $validated['coach_slot_end'] ?? null;

            if (! $slotStart || ! $slotEnd) {
                throw ValidationException::withMessages([
                    'coach_slot_start' => 'Pilih slot jam untuk coach yang dipilih.',
                ]);
            }

            if (! $coach->isAvailableOnDay($bookingDay) || ! $coach->isTimeInSession($bookingDay, $slotStart)) {
                throw ValidationException::withMessages([
                    'coach_slot_start' => 'Slot jam yang dipilih tidak tersedia untuk coach ini.',
                ]);
            }

            // Check slot not already taken by another user
            $slotTaken = Reservation::query()
                ->where('coach_id', $coach->id)
                ->whereDate('tanggal_booking', $validated['tanggal_booking'])
                ->whereIn('status_reservasi', ['confirmed', 'completed'])
                ->where('jam_mulai', '<', $slotEnd)
                ->where('jam_selesai', '>', $slotStart)
                ->exists();

            if ($slotTaken) {
                throw ValidationException::withMessages([
                    'coach_slot_start' => 'Slot jam coach sudah dipesan. Pilih slot lain.',
                ]);
            }
        }

        $totalHargaLapangan = $court->priceForRange($mulai, $selesai);
        $totalHargaCoach = $coach ? (int) $coach->harga_per_jam : 0;

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

        if ($pointToUse > 0 && ! Auth::user()->membership) {
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
                'jam_mulai' => $validated['coach_id'] ? ($validated['coach_slot_start'] ?? $validated['jam_mulai']) : $validated['jam_mulai'],
                'jam_selesai' => $validated['coach_id'] ? ($validated['coach_slot_end'] ?? $validated['jam_selesai']) : $validated['jam_selesai'],
                'status_reservasi' => 'confirmed',
                'batas_pembayaran' => null,
            ]);

            if (! empty($pivotRows)) {
                $reservation->equipment()->attach($pivotRows);
            }

            $pointDiscount = 0;
            $membership = null;
            if ($pointToUse > 0) {
                $membership = Membership::query()
                    ->where('user_id', Auth::id())
                    ->lockForUpdate()
                    ->first();

                if (! $membership) {
                    throw ValidationException::withMessages([
                        'point_to_use' => 'Membership tidak ditemukan.',
                    ]);
                }

                $availablePoints = (int) $membership->total_poin_aktif;
                if ($pointToUse > $availablePoints) {
                    throw ValidationException::withMessages([
                        'point_to_use' => 'Poin tidak mencukupi. Sisa poin Anda: '.number_format($availablePoints, 0, ',', '.'),
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
                    'keterangan' => 'Penukaran poin untuk transaksi #'.$transaction->id,
                ]);
            }

            return $transaction;
        });

        // Redirect ke payment page
        return redirect()->route('payment.page', $transaction->id);
    }
}
