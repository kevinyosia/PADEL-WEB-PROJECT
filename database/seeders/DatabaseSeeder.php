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
            UserSeeder::class,
            CourtSeeder::class,
            CoachSeeder::class,
            EquipmentSeeder::class,
            // ConsumableSeeder::class, // MERGED INTO EQUIPMENT SEEDER
            // RentalItemSeeder::class, // MERGED INTO EQUIPMENT SEEDER
        ]);
        
        Reservation::factory(30)->create();
    }
}