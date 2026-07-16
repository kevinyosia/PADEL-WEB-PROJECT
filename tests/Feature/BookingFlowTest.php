<?php

use App\Models\Coach;
use App\Models\Court;
use App\Models\Reservation;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;

test('booking store confirms and marks payment as paid immediately', function () {
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
        'payment_channel' => 'virtual_account',
    ]);

    $reservation = Reservation::first();

    $response->assertRedirect(route('booking.index'));
    expect($reservation)->not->toBeNull();
    expect($reservation->status_reservasi)->toBe('confirmed');
    expect($reservation->batas_pembayaran)->toBeNull();

    $transaction = Transaction::where('reservation_id', $reservation->id)->first();
    expect($transaction)->not->toBeNull();
    expect($transaction->metode_pembayaran)->toBe('transfer');
    expect($transaction->channel_pembayaran)->toBe('virtual_account');
    expect($transaction->status_pembayaran)->toBe('lunas');

    Carbon::setTestNow();
});

test('booking store rejects slot that is already confirmed', function () {
    $user = User::factory()->create();

    $court = Court::create([
        'nama_lapangan' => 'Court A',
        'deskripsi' => 'Test court',
        'harga_pagi_tengahmalam' => 100000,
        'harga_malam' => 120000,
        'harga_weekend' => 140000,
        'status' => 'tersedia',
    ]);

    Reservation::create([
        'user_id' => $user->id,
        'court_id' => $court->id,
        'coach_id' => null,
        'tanggal_booking' => '2026-04-17',
        'jam_mulai' => '10:00',
        'jam_selesai' => '11:00',
        'status_reservasi' => 'confirmed',
        'batas_pembayaran' => null,
    ]);

    $response = $this->actingAs($user)->post(route('booking.store'), [
        'court_id' => $court->id,
        'tanggal_booking' => '2026-04-17',
        'jam_mulai' => '10:00',
        'jam_selesai' => '11:00',
        'payment_channel' => 'virtual_account',
    ]);

    $response->assertStatus(302)->assertSessionHasErrors('jam_mulai');
});

test('courts availability returns only available booked maintenance statuses', function () {
    $user = User::factory()->create();

    $availableCourt = Court::create([
        'nama_lapangan' => 'Court 1',
        'deskripsi' => 'A',
        'harga_pagi_tengahmalam' => 275000,
        'harga_malam' => 388000,
        'harga_weekend' => 300000,
        'status' => 'tersedia',
    ]);

    $maintenanceCourt = Court::create([
        'nama_lapangan' => 'Court 2',
        'deskripsi' => 'B',
        'harga_pagi_tengahmalam' => 275000,
        'harga_malam' => 388000,
        'harga_weekend' => 300000,
        'status' => 'maintenance',
    ]);

    Reservation::create([
        'user_id' => $user->id,
        'court_id' => $availableCourt->id,
        'coach_id' => null,
        'tanggal_booking' => '2026-04-20',
        'jam_mulai' => '18:00',
        'jam_selesai' => '19:00',
        'status_reservasi' => 'confirmed',
        'batas_pembayaran' => null,
    ]);

    $response = $this->actingAs($user)->get(route('courts.availability', ['date' => '2026-04-20']));

    $response->assertOk();
    $json = $response->json();

    $courtA = collect($json['courts'])->firstWhere('id', $availableCourt->id);
    $courtB = collect($json['courts'])->firstWhere('id', $maintenanceCourt->id);

    $slot18 = collect($courtA['slots'])->firstWhere('start', '18:00');
    $slot10 = collect($courtA['slots'])->firstWhere('start', '10:00');

    expect($slot18['status'])->toBe('booked');
    expect($slot18['price'])->toBe(388000);
    expect($slot10['status'])->toBe('available');
    expect($slot10['price'])->toBe(275000);

    expect(collect($courtB['slots'])->pluck('status')->unique()->values()->all())->toBe(['maintenance']);
});

test('booking store with valid coach session slot succeeds', function () {
    Carbon::setTestNow('2026-04-16 10:00:00');

    $user = User::factory()->create();
    $coachUser = User::factory()->create(['role' => 'coach']);
    $coach = Coach::create([
        'user_id' => $coachUser->id,
        'deskripsi_keahlian' => 'Expert coach',
        'harga_per_jam' => 150000,
        'availability_status' => 'active',
        'schedule' => [
            'fri' => ['active' => true, 'sessions' => [['start' => '13:00', 'end' => '16:00']]],
            'mon' => ['active' => false, 'sessions' => []],
            'tue' => ['active' => false, 'sessions' => []],
            'wed' => ['active' => false, 'sessions' => []],
            'thu' => ['active' => false, 'sessions' => []],
        ],
    ]);

    $court = Court::create([
        'nama_lapangan' => 'Court A',
        'deskripsi' => 'Test court',
        'harga_pagi_tengahmalam' => 100000,
        'harga_malam' => 120000,
        'harga_weekend' => 140000,
        'status' => 'tersedia',
    ]);

    // 2026-04-17 is Friday
    $response = $this->actingAs($user)->post(route('booking.store'), [
        'court_id' => $court->id,
        'tanggal_booking' => '2026-04-17',
        'jam_mulai' => '13:00',
        'jam_selesai' => '14:00',
        'coach_id' => $coach->id,
        'coach_slot_start' => '13:00',
        'coach_slot_end' => '14:00',
        'payment_channel' => 'virtual_account',
    ]);

    $response->assertRedirect();
    $reservation = Reservation::first();
    expect($reservation)->not->toBeNull();
    expect($reservation->coach_id)->toBe($coach->id);
    expect($reservation->jam_mulai)->toBe('13:00:00');
    expect($reservation->jam_selesai)->toBe('14:00:00');

    Carbon::setTestNow();
});

test('booking store with coach but missing slot fails', function () {
    Carbon::setTestNow('2026-04-16 10:00:00');

    $user = User::factory()->create();
    $coachUser = User::factory()->create(['role' => 'coach']);
    $coach = Coach::create([
        'user_id' => $coachUser->id,
        'deskripsi_keahlian' => 'Expert coach',
        'harga_per_jam' => 150000,
        'availability_status' => 'active',
        'schedule' => [
            'fri' => ['active' => true, 'sessions' => [['start' => '13:00', 'end' => '16:00']]],
            'mon' => ['active' => false, 'sessions' => []],
            'tue' => ['active' => false, 'sessions' => []],
            'wed' => ['active' => false, 'sessions' => []],
            'thu' => ['active' => false, 'sessions' => []],
        ],
    ]);

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
        'jam_mulai' => '13:00',
        'jam_selesai' => '14:00',
        'coach_id' => $coach->id,
        'payment_channel' => 'virtual_account',
    ]);

    $response->assertStatus(302)->assertSessionHasErrors('coach_slot_start');

    Carbon::setTestNow();
});

test('booking store with coach but invalid slot time fails', function () {
    Carbon::setTestNow('2026-04-16 10:00:00');

    $user = User::factory()->create();
    $coachUser = User::factory()->create(['role' => 'coach']);
    $coach = Coach::create([
        'user_id' => $coachUser->id,
        'deskripsi_keahlian' => 'Expert coach',
        'harga_per_jam' => 150000,
        'availability_status' => 'active',
        'schedule' => [
            'fri' => ['active' => true, 'sessions' => [['start' => '13:00', 'end' => '16:00']]],
            'mon' => ['active' => false, 'sessions' => []],
            'tue' => ['active' => false, 'sessions' => []],
            'wed' => ['active' => false, 'sessions' => []],
            'thu' => ['active' => false, 'sessions' => []],
        ],
    ]);

    $court = Court::create([
        'nama_lapangan' => 'Court A',
        'deskripsi' => 'Test court',
        'harga_pagi_tengahmalam' => 100000,
        'harga_malam' => 120000,
        'harga_weekend' => 140000,
        'status' => 'tersedia',
    ]);

    // Slot 12:00 is outside coach's 13:00-16:00 session
    $response = $this->actingAs($user)->post(route('booking.store'), [
        'court_id' => $court->id,
        'tanggal_booking' => '2026-04-17',
        'jam_mulai' => '12:00',
        'jam_selesai' => '13:00',
        'coach_id' => $coach->id,
        'coach_slot_start' => '12:00',
        'coach_slot_end' => '13:00',
        'payment_channel' => 'virtual_account',
    ]);

    $response->assertStatus(302)->assertSessionHasErrors('coach_slot_start');

    Carbon::setTestNow();
});
