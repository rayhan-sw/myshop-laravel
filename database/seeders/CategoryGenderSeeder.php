<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

// Seeder untuk mengisi kategori awal berdasarkan gender
class CategoryGenderSeeder extends Seeder
{
    public function run(): void
    {
        $pria   = Category::firstOrCreate(['name' => 'Pria', 'parent_id' => null]); // Kategori utama untuk Pria
        $wanita = Category::firstOrCreate(['name' => 'Wanita', 'parent_id' => null]); // Kategori utama untuk Wanita

        Category::firstOrCreate(['name' => 'Kemeja', 'parent_id' => $pria->id]); // Subkategori Kemeja untuk Pria
        Category::firstOrCreate(['name' => 'Celana', 'parent_id' => $pria->id]); // Subkategori Celana untuk Pria

        Category::firstOrCreate(['name' => 'Rok', 'parent_id' => $wanita->id]); // Subkategori Rok untuk Wanita
        Category::firstOrCreate(['name' => 'Blouse', 'parent_id' => $wanita->id]); // Subkategori Blouse untuk Wanita
    }
}
