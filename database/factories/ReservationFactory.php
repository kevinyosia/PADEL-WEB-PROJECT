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
        $tanggal = $this->faker->dateTimeBetween('now', '+14 days')->format('Y-m-d');
        $jamInt = $this->faker->numberBetween(6, 23); 
        $jamMulai = str_pad($jamInt, 2, '0', STR_PAD_LEFT) . ':00:00';
        
        $durasi = ($jamInt === 23) ? 1 : $this->faker->randomElement([1, 2]);
        $jamSelesai = str_pad($jamInt + $durasi, 2, '0', STR_PAD_LEFT) . ':00:00';

        // --- INI PENYESUAIANNYA ---
        // Jika jam di bawah 8 pagi atau di atas 19 (7 malam), maka TIDAK BOLEH ada coach
        $coach_id = null;
        if ($jamInt >= 8 && $jamInt <= 19) {
            // Jika masuk jam kerja coach, ada kemungkinan 40% orang booking dengan coach
            $coach_id = $this->faker->boolean(40) ? Coach::inRandomOrder()->first()->id : null;
        }

        return [
            'user_id'          => User::inRandomOrder()->first()->id ?? User::factory(),
            'court_id'         => Court::inRandomOrder()->first()->id ?? 1,
            'coach_id'         => $coach_id, // Gunakan variabel yang sudah divalidasi di atas
            'tanggal_booking'  => $tanggal,
            'jam_mulai'        => $jamMulai,
            'jam_selesai'      => $jamSelesai,
            'status_reservasi' => $this->faker->randomElement(['confirmed', 'completed', 'cancelled']),
        ];
    }
}