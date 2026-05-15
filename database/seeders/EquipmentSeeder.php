<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Equipment;

class EquipmentSeeder extends Seeder
{
    public function run(): void
    {
        // Regular equipment (beli/sewa from original seeder)
        $equipments = [
            // Sales items (formerly in Consumable seeder)
            [
                'nama_alat' => 'Wilson Triniti (3-Pack)',
                'kategori'  => 'beli',
                'harga'     => 250000,
                'deskripsi' => 'Premium badminton birdies pack',
                'sku' => 'WLS-TRN-3P',
                'stock_quantity' => 124,
                'max_capacity' => 200,
                'condition' => 'good',
                'rental_rate' => null,
            ],
            [
                'nama_alat' => 'Head Pro Padel (3-Pack)',
                'kategori'  => 'beli',
                'harga'     => 280000,
                'deskripsi' => 'Professional padel balls',
                'sku' => 'HED-PRO-3P',
                'stock_quantity' => 12,
                'max_capacity' => 100,
                'condition' => 'good',
                'rental_rate' => null,
            ],
            [
                'nama_alat' => 'Bullpadel Gold (3-Pack)',
                'kategori'  => 'beli',
                'harga'     => 150000,
                'deskripsi' => 'Budget-friendly padel balls',
                'sku' => 'BUL-GLD-3P',
                'stock_quantity' => 0,
                'max_capacity' => 100,
                'condition' => 'good',
                'rental_rate' => null,
            ],
            // Merged from RentalItemSeeder
            [
                'nama_alat' => 'Bullpadel Vertex 03',
                'kategori' => 'sewa',
                'harga' => 50000,
                'deskripsi' => 'Pro performance series',
                'sku' => 'BUL-VTX-03',
                'stock_quantity' => 18,
                'max_capacity' => 20,
                'condition' => 'excellent',
                'rental_rate' => 50000,
            ],
            [
                'nama_alat' => 'Slinger Bag Padel',
                'kategori' => 'sewa',
                'harga' => 80000,
                'deskripsi' => 'Automatic ball launcher',
                'sku' => 'SLG-BAG-01',
                'stock_quantity' => 1,
                'max_capacity' => 4,
                'condition' => 'maintenance',
                'rental_rate' => 80000,
            ],
            [
                'nama_alat' => 'Adidas Adipower Lite',
                'kategori' => 'sewa',
                'harga' => 40000,
                'deskripsi' => 'Intermediate round shape',
                'sku' => 'ADS-APL-01',
                'stock_quantity' => 25,
                'max_capacity' => 25,
                'condition' => 'good',
                'rental_rate' => 40000,
            ],
        ];

        foreach ($equipments as $equipment) {
            Equipment::updateOrCreate(
                ['nama_alat' => $equipment['nama_alat']],
                [
                    'kategori'  => $equipment['kategori'],
                    'harga'     => $equipment['harga'],
                    'deskripsi' => $equipment['deskripsi'],
                    'sku' => $equipment['sku'],
                    'stock_quantity' => $equipment['stock_quantity'],
                    'max_capacity' => $equipment['max_capacity'],
                    'condition' => $equipment['condition'],
                    'rental_rate' => $equipment['rental_rate'],
                ]
            );
        }
    }
}
