<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategoriesTableSeeder extends Seeder
{
    public function run()
    {
        // Catégories principales
        $electronics = Category::create(['name' => 'Électronique']);
        $fashion = Category::create(['name' => 'Mode']);

        // Sous-catégories d'Électronique
        $phones = Category::create(['name' => 'Téléphones', 'parent_id' => $electronics->id]);
        $computers = Category::create(['name' => 'Ordinateurs', 'parent_id' => $electronics->id]);

        // Sous-sous-catégories de Téléphones
        Category::create(['name' => 'Smartphones', 'parent_id' => $phones->id]);
        Category::create(['name' => 'Téléphones fixes', 'parent_id' => $phones->id]);

        // Sous-sous-catégories d'Ordinateurs
        Category::create(['name' => 'PC portables', 'parent_id' => $computers->id]);
        Category::create(['name' => 'PC de bureau', 'parent_id' => $computers->id]);

        // Sous-catégories de Mode
        $menFashion = Category::create(['name' => 'Mode homme', 'parent_id' => $fashion->id]);
        $womenFashion = Category::create(['name' => 'Mode femme', 'parent_id' => $fashion->id]);

        // Sous-sous-catégories de Mode homme
        Category::create(['name' => 'Costumes', 'parent_id' => $menFashion->id]);
        Category::create(['name' => 'Jeans', 'parent_id' => $menFashion->id]);

        // Sous-sous-catégories de Mode femme
        Category::create(['name' => 'Robes', 'parent_id' => $womenFashion->id]);
        Category::create(['name' => 'Jupes', 'parent_id' => $womenFashion->id]);
    }
}
