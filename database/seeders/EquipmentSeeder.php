<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Equipment;

class EquipmentSeeder extends Seeder
{
    public function run(): void
    {
        $equipments = [
            [
                'nama_alat' => 'Sewa Raket Padel',
                'kategori'  => 'sewa',
                'harga'     => 50000,
                'deskripsi' => 'Harga per sesi (1 jam bermain).'
            ],
            [
                'nama_alat' => 'Bola Padel (Brand A)',
                'kategori'  => 'beli',
                'harga'     => 100000,
                'deskripsi' => 'Pembelian 1 slop bola padel standar.'
            ],
            [
                'nama_alat' => 'Bola Padel Pro (Brand B)',
                'kategori'  => 'beli',
                'harga'     => 150000,
                'deskripsi' => 'Pembelian 1 slop bola padel kualitas turnamen.'
            ]
        ];

        foreach ($equipments as $equipment) {
            Equipment::updateOrCreate(
                ['nama_alat' => $equipment['nama_alat']],
                [
                    'kategori'  => $equipment['kategori'],
                    'harga'     => $equipment['harga'],
                    'deskripsi' => $equipment['deskripsi'],
                ]
            );
        }
    }
}