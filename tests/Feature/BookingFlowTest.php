<?php

use App\Models\Coach;
use App\Models\Court;
use App\Models\Equipment;
use App\Models\Membership;
use App\Models\PointHistory;
use App\Models\Reservation;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;

test('non member gets 8 hour payment deadline when creating reservation', function () {
    Carbon::setTestNow('2026-04-16 10:00:00');

    $user = User::factory()->create();
    $court = Court::create([
        'nama_lapangan' => 'Court A',
        'deskripsi' => 'Test court',
        'harga_pagi_tengahmalam' => 100000,
        'harga_malam' => 120000,
        'harga_weekend' => 140000,
        'status' => 'tersedia',
    ]);

    $response = $this->actingAs($user)->post(route('booking.store'), [
        'court_id' => $court->id,
        'tanggal_booking' => '2026-04-17',
        'jam_mulai' => '10:00',
        'jam_selesai' => '12:00',
    ]);

    $reservation = Reservation::first();

    $response->assertRedirect(route('booking.index'));
    expect($reservation)->not->toBeNull();
    expect($reservation->status_reservasi)->toBe('pending');
    expect($reservation->batas_pembayaran->toDateTimeString())->toBe('2026-04-16 18:00:00');

    $transaction = Transaction::where('reservation_id', $reservation->id)->first();
    expect($transaction)->not->toBeNull();
    expect($transaction->metode_pembayaran)->toBe('transfer');
    expect($transaction->status_pembayaran)->toBe('belum_lunas');

    Carbon::setTestNow();
});

test('member payment via transfer applies cashback and writes point history in and out', function () {
    Carbon::setTestNow('2026-04-16 10:00:00');

    $user = User::factory()->create();

    Membership::create([
        'user_id' => $user->id,
        'total_poin_aktif' => 100,
        'total_poin_terpakai' => 0,
    ]);

    $court = Court::create([
        'nama_lapangan' => 'Court B',
        'deskripsi' => 'Test court',
        'harga_pagi_tengahmalam' => 100000,
        'harga_malam' => 120000,
        'harga_weekend' => 140000,
        'status' => 'tersedia',
    ]);

    $coachUser = User::factory()->create();
    Coach::create([
        'user_id' => $coachUser->id,
        'deskripsi_keahlian' => 'Coach test',
        'harga_per_jam' => 100000,
    ]);

    $racket = Equipment::create([
        'nama_alat' => 'Sewa Raket',
        'kategori' => 'sewa',
        'harga' => 50000,
        'deskripsi' => 'Rental racket',
    ]);

    $balls = Equipment::create([
        'nama_alat' => 'Bola Padel',
        'kategori' => 'beli',
        'harga' => 100000,
        'deskripsi' => 'Buy balls',
    ]);

    $this->actingAs($user)->post(route('booking.store'), [
        'court_id' => $court->id,
        'tanggal_booking' => '2026-04-17',
        'jam_mulai' => '10:00',
        'jam_selesai' => '11:00',
        'equipment_items' => [
            ['equipment_id' => $racket->id, 'jumlah' => 1],
            ['equipment_id' => $balls->id, 'jumlah' => 1],
        ],
    ])->assertRedirect(route('booking.index'));

    $reservation = Reservation::first();

    $payResponse = $this->actingAs($user)->post(route('booking.pay', $reservation), [
        'payment_channel' => 'virtual_account',
        'use_points' => 20,
    ]);

    $payResponse->assertRedirect(route('booking.index'));

    $reservation->refresh();
    expect($reservation->status_reservasi)->toBe('confirmed');

    $transaction = Transaction::where('reservation_id', $reservation->id)->first();
    expect($transaction->metode_pembayaran)->toBe('transfer');
    expect($transaction->channel_pembayaran)->toBe('virtual_account');
    expect($transaction->status_pembayaran)->toBe('lunas');

    $membership = Membership::where('user_id', $user->id)->first();
    expect($membership->total_poin_aktif)->toBe(17080);
    expect($membership->total_poin_terpakai)->toBe(20);

    expect(PointHistory::where('user_id', $user->id)->where('jumlah_poin', -20)->exists())->toBeTrue();
    expect(PointHistory::where('user_id', $user->id)->where('jumlah_poin', 17000)->exists())->toBeTrue();

    Carbon::setTestNow();
});
