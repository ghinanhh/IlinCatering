<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run()
    {
        // Otomatis mendaftarkan kategori utama katering Ghina
        $categories = ['Nasi Box', 'Prasmanan', 'Snack Box'];

        foreach ($categories as $cat) {
            Category::firstOrCreate(['name' => $cat]);
        }
    }
}
