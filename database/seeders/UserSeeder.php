<?php

namespace Database\Seeders;

use App\Models\User; // Penting: Menghubungkan ke Model User
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash; // Penting: Untuk mengenkripsi password

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Akun Owner (Cuma satu)
        User::create([
            'name'     => 'Owner Ilin Catering',
            'email'    => 'owner@ilin.com',
            'password' => Hash::make('password123'),
            'role'     => 'owner',
        ]);

        // 2. Akun Admin (Karyawan)
        User::create([
            'name'     => 'Admin Ilin',
            'email'    => 'admin@ilin.com',
            'password' => Hash::make('password123'),
            'role'     => 'admin',
        ]);

        // 3. Contoh Akun Pelanggan
        User::create([
            'name'     => 'Pelanggan Ghina',
            'email'    => 'ghina@mail.com',
            'password' => Hash::make('password123'),
            'role'     => 'pelanggan',
        ]);
    }
}