<?php

namespace Database\Seeders;

use App\Models\Consumable;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ConsumableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Consumable::updateOrCreate(
            ['sku' => 'WLS-TRN-3P'],
            [
                'name' => 'Wilson Triniti (3-Pack)',
                'description' => 'Premium badminton birdies pack',
                'purchase_price' => 250000,
                'stock_quantity' => 124,
                'max_capacity' => 200,
            ]
        );

        Consumable::updateOrCreate(
            ['sku' => 'HED-PRO-3P'],
            [
                'name' => 'Head Pro Padel (3-Pack)',
                'description' => 'Professional padel balls',
                'purchase_price' => 280000,
                'stock_quantity' => 12,
                'max_capacity' => 100,
            ]
        );

        Consumable::updateOrCreate(
            ['sku' => 'BUL-GLD-3P'],
            [
                'name' => 'Bullpadel Gold (3-Pack)',
                'description' => 'Budget-friendly padel balls',
                'purchase_price' => 150000,
                'stock_quantity' => 0,
                'max_capacity' => 100,
            ]
        );
    }
}
