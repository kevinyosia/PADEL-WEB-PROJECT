<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Coach;
use App\Models\User; // Jangan lupa ini wajib ditambahkan
use Illuminate\Support\Facades\Hash; // Ini juga wajib untuk password dummy

class CoachSeeder extends Seeder
{
    public function run(): void
    {
        $coaches = [
            [
                'nama'          => 'Coach Josan',
                'deskripsi'     => 'Straight from Club de Campo in Elche, Spain, Coach Josan brings years of experience coaching padel and tennis.',
                'harga'         => 800000,
            ],
            [
                'nama'          => 'Coach Gerry',
                'deskripsi'     => 'Specializes in tailored training sessions that focuses on individual strengths and development areas.',
                'harga'         => 600000,
            ],
            [
                'nama'          => 'Coach Akses',
                'deskripsi'     => 'Looking to elevate your padel game? Train with Coach Akses, a seasoned athlete and passionate coach.',
                'harga'         => 450000,
            ],
            [
                'nama'          => 'Coach Bintang',
                'deskripsi'     => 'A former national junior tennis athlete with over three years of coaching experience, certified by PadelMBA.',
                'harga'         => 400000,
            ],
            [
                'nama'          => 'Coach Richard',
                'deskripsi'     => 'With a decorated background in competitive sports and a sharp eye for game dynamics.',
                'harga'         => 375000,
            ],
            [
                'nama'          => 'Coach Krisna',
                'deskripsi'     => 'Our young, aspiring, up-and-coming padel coach. With a decade of experience as an accomplished badminton player.',
                'harga'         => 300000,
            ]
        ];

        foreach ($coaches as $index => $coachData) {
            $user = User::create([
                'name'     => $coachData['nama'],
                'email'    => 'coach' . ($index + 1) . '@sistem.local', 
                'phone'    => '-', 
                'password' => Hash::make('rahasia123'),
                'role'     => 'coach',
            ]);
            Coach::create([
                'user_id'            => $user->id,
                'deskripsi_keahlian' => $coachData['deskripsi'],
                'harga_per_jam'      => $coachData['harga'],
            ]);
        }
    }
}