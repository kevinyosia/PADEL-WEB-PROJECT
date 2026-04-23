<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Models\Reservation;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('reservations:cancel-expired', function () {
    $expiredReservationIds = Reservation::query()
        ->where('status_reservasi', 'pending')
        ->whereNotNull('batas_pembayaran')
        ->where('batas_pembayaran', '<', now())
        ->pluck('id');

    $cancelledCount = Reservation::query()
        ->whereIn('id', $expiredReservationIds)
        ->update(['status_reservasi' => 'cancelled']);

    \App\Models\Transaction::query()
        ->whereIn('reservation_id', $expiredReservationIds)
        ->update(['status_pembayaran' => 'belum_lunas']);

    $this->info("{$cancelledCount} reservasi kedaluwarsa berhasil dibatalkan.");
})->purpose('Membatalkan reservasi pending yang melewati batas pembayaran');

Schedule::command('reservations:cancel-expired')->everyMinute();
