<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Membership;
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
        User::updateOrCreate(
            ['email' => '123@admin.local'],
            [
                'name' => 'Messi',
                'phone' => null,
                'password' => Hash::make('321'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        // Create manager account
        User::updateOrCreate(
            ['email' => 'manager@bandeja.local'],
            [
                'name' => 'Manager Bandeja',
                'phone' => null,
                'password' => Hash::make('manager123'),
                'role' => 'manajemen',
                'email_verified_at' => now(),
            ]
        );

        // Create regular customer account
        User::updateOrCreate(
            ['email' => 'customer@example.com'],
            [
                'name' => 'kevin',
                'phone' => '08123456789',
                'password' => Hash::make('password'),
                'role' => 'customer',
                'email_verified_at' => now(),
            ]
        );

        // Create customer with membership
        $memberUser = User::updateOrCreate(
            ['email' => 'member@bandeja.com'],
            [
                'name' => 'Budi Member',
                'phone' => '08987654321',
                'password' => Hash::make('member123'),
                'role' => 'customer',
                'email_verified_at' => now(),
            ]
        );

        // Create membership for this user
        Membership::updateOrCreate(
            ['user_id' => $memberUser->id],
            [
                'total_poin_aktif' => 5000,
                'total_poin_terpakai' => 1200,
            ]
        );
    }
}
