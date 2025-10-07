<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategoryGenderSeeder extends Seeder
{
    public function run(): void
    {
        $pria   = Category::firstOrCreate(['name'=>'Pria','parent_id'=>null]);
        $wanita = Category::firstOrCreate(['name'=>'Wanita','parent_id'=>null]);

        Category::firstOrCreate(['name'=>'Kemeja', 'parent_id'=>$pria->id]);
        Category::firstOrCreate(['name'=>'Celana', 'parent_id'=>$pria->id]);

        Category::firstOrCreate(['name'=>'Rok',    'parent_id'=>$wanita->id]);
        Category::firstOrCreate(['name'=>'Blouse', 'parent_id'=>$wanita->id]);
    }
}
