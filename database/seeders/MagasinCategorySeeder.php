<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Magasin;
use App\Models\Category;

class MagasinCategorySeeder extends Seeder
{
    public function run()
    {
        $magasin1 = Magasin::where('nom', 'Magasin Électronique 1')->first();
        $magasin2 = Magasin::where('nom', 'Magasin Mode 1')->first();
        $magasin3 = Magasin::where('nom', 'Magasin Général')->first();

        $smartphones = Category::where('name', 'Smartphones')->first();
        $costumes = Category::where('name', 'Costumes')->first();
        $pcPortables = Category::where('name', 'PC portables')->first();
        $robes = Category::where('name', 'Robes')->first();

        // Associer les catégories aux magasins
        $magasin1->attachCategory($smartphones);
        $magasin1->attachCategory($pcPortables);
        $magasin2->attachCategory($costumes);
        $magasin2->attachCategory($robes);
        $magasin3->attachCategory($smartphones);
        $magasin3->attachCategory($robes);
    }
}
