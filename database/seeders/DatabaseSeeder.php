<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Reservation;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            CourtSeeder::class,
            CoachSeeder::class,
            EquipmentSeeder::class,
        ]);        
        User::factory(10)->create();
        Reservation::factory(30)->create();
    }
}