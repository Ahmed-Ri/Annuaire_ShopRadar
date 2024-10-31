<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Magasin;

class MagasinsTableSeeder extends Seeder
{
    public function run()
    {
        Magasin::create([
            'nom' => 'Magasin Électronique 1',
            'adresse' => '123 Rue de la Technologie',
            'tel' => '0123456789',
            'secteur' => 'Électronique',
            'image' => null,
            'tag' => 'High-Tech'
        ]);

        Magasin::create([
            'nom' => 'Magasin Mode 1',
            'adresse' => '456 Avenue du Style',
            'tel' => '0987654321',
            'secteur' => 'Mode',
            'image' => null,
            'tag' => 'Fashion'
        ]);

        Magasin::create([
            'nom' => 'Magasin Général',
            'adresse' => '789 Boulevard du Commerce',
            'tel' => '0123987654',
            'secteur' => 'Multi-sectoriel',
            'image' => null,
            'tag' => 'General'
        ]);
    }
}

