<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin account
        User::create([
            'name' => 'Messi',
            'email' => '123@admin.local',
            'phone' => null,
            'password' => Hash::make('321'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        // Create sample customer account (optional)
        User::create([
            'name' => 'kevin',
            'email' => 'customer@example.com',
            'phone' => '08123456789',
            'password' => Hash::make('password'),
            'role' => 'customer',
            'email_verified_at' => now(),
        ]);
    }
}
