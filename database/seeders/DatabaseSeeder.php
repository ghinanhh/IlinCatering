<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Panggil UserSeeder agar akun Owner, Admin, dan Pelanggan yang kamu buat tadi dijalankan
        $this->call([
            UserSeeder::class,
        ]);
    }
}