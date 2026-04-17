<?php

namespace Database\Seeders;

use App\Models\RentalItem;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RentalItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        RentalItem::create([
            'name' => 'Bullpadel Vertex 03',
            'sku' => 'BUL-VTX-03',
            'description' => 'Pro performance series',
            'category' => 'Racket',
            'rental_rate' => 50000,
            'stock_quantity' => 18,
            'max_capacity' => 20,
            'condition' => 'excellent',
        ]);

        RentalItem::create([
            'name' => 'Slinger Bag Padel',
            'sku' => 'SLG-BAG-01',
            'description' => 'Automatic ball launcher',
            'category' => 'Machine',
            'rental_rate' => 80000,
            'stock_quantity' => 1,
            'max_capacity' => 4,
            'condition' => 'maintenance',
        ]);

        RentalItem::create([
            'name' => 'Adidas Adipower Lite',
            'sku' => 'ADS-APL-01',
            'description' => 'Intermediate round shape',
            'category' => 'Racket',
            'rental_rate' => 40000,
            'stock_quantity' => 25,
            'max_capacity' => 25,
            'condition' => 'good',
        ]);
    }
}
