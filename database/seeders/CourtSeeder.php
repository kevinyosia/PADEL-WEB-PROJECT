<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Court;

class CourtSeeder extends Seeder
{
    public function run(): void
    {
        $courts = [];
        for ($i = 1; $i <= 6; $i++) {
            $courts[] = [
                'nama_lapangan' => 'Bandeja Padel Court ' . $i,
                'tipe_lapangan' => 'Padel Court',
                'harga_pagi_tengahmalam' => 275000,
                'harga_malam' => 388000,
                'harga_weekend' => 300000,
                'deskripsi' => 'Tersedia'
            ];
        }

        foreach ($courts as $court) {
            Court::create($court);
        }
    }
}