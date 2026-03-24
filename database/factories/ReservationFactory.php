<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Court;
use App\Models\Coach;

// PASTIKAN NAMA CLASS-NYA ReservationFactory
class ReservationFactory extends Factory 
{
    public function definition(): array
    {
        // Acak Tanggal: hari ini sampai 14 hari ke depan
        $tanggal = $this->faker->dateTimeBetween('now', '+14 days')->format('Y-m-d');

        // Acak Jam Mulai: 06:00 sampai 23:00 (karena operasional tutup 24:00)
        $jamInt = $this->faker->numberBetween(6, 23);
        $jamMulai = str_pad($jamInt, 2, '0', STR_PAD_LEFT) . ':00:00';
        
        // Asumsi durasi main 1 jam (bisa 2 jam jika belum lewat jam 24:00)
        $durasi = ($jamInt === 23) ? 1 : $this->faker->randomElement([1, 2]);
        $jamSelesai = str_pad($jamInt + $durasi, 2, '0', STR_PAD_LEFT) . ':00:00';

        return [
            'user_id'          => User::inRandomOrder()->first()->id ?? User::factory(),
            'court_id'         => Court::inRandomOrder()->first()->id ?? 1,
            'coach_id'         => $this->faker->boolean(40) ? Coach::inRandomOrder()->first()->id : null,
            'tanggal_booking'  => $tanggal,
            'jam_mulai'        => $jamMulai,
            'jam_selesai'      => $jamSelesai,
            'status_reservasi' => $this->faker->randomElement(['pending', 'confirmed', 'completed', 'cancelled']),
        ];
    }
}