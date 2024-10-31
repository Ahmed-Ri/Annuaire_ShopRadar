<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ImportController extends Controller
{
    public function importCategoriesFromCsv()
    {
        // Chemin complet du fichier CSV dans `storage/app`
        $filePath = storage_path('app/categories.csv');
    
        // Vérifiez que le fichier existe avant de continuer
        if (!file_exists($filePath)) {
            return response()->json(['error' => 'Le fichier CSV est introuvable.'], 404);
        }
    
        // Lire le contenu du fichier et supprimer le BOM s'il est présent
        $fileContent = file_get_contents($filePath);
        $fileContent = preg_replace('/^\x{FEFF}/u', '', $fileContent); // Supprime le BOM
        $csvData = array_map('str_getcsv', explode("\n", $fileContent));
        
        // Enlève la première ligne contenant les en-têtes
        $headers = array_shift($csvData);
    
        foreach ($csvData as $row) {
            // Ignore les lignes vides ou incomplètes
            if (count($row) !== count($headers)) {
                continue;
            }
    
            $rowData = array_combine($headers, $row);
    
            // Vérifiez que toutes les clés sont définies dans $rowData
            if (!isset($rowData['magasin_id']) || !isset($rowData['category_id']) || !isset($rowData['type'])) {
                continue;
            }
    
            $magasinId = $rowData['magasin_id'];
            $categoryId = $rowData['category_id'];
            $type = $rowData['type'];
    
            // Insérer la hiérarchie en fonction du type
            if ($type === 'category') {
                $this->insertCategoryHierarchy($magasinId, $categoryId, true);
            } elseif ($type === 'subcategory') {
                $this->insertCategoryHierarchy($magasinId, $categoryId, false);
            } elseif ($type === 'subsubcategory') {
                $this->insertSubsubcategory($magasinId, $categoryId);
            }
        }
    
        return response()->json(['message' => 'Importation terminée avec succès']);
    }
    
    // Fonction pour insérer les sous-catégories et sous-sous-catégories uniquement
    private function insertCategoryHierarchy($magasinId, $categoryId, $isMainCategory)
    {
        // Récupère la catégorie de base
        $category = Category::find($categoryId);
    
        if (!$category) {
            return; // Si la catégorie n'existe pas, arrêtez l'exécution
        }
    
        // Logique d'insertion pour les sous-catégories et sous-sous-catégories
        if ($isMainCategory) {
            // Pour une catégorie principale, insérer toutes les sous-catégories et sous-sous-catégories
            foreach ($category->children as $subcat) {
                // Insertion de chaque sous-catégorie
                DB::table('magasin_category')->insert([
                    'magasin_id' => $magasinId,
                    'category_id' => $subcat->id,
                    'subcategory_id' => $subcat->id,
                    'main_category_id' => $categoryId,
                    'type' => 'subcategory',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
    
                // Insertion des sous-sous-catégories pour chaque sous-catégorie
                foreach ($subcat->children as $subsubcat) {
                    DB::table('magasin_category')->insert([
                        'magasin_id' => $magasinId,
                        'category_id' => $subsubcat->id,
                        'subcategory_id' => $subcat->id,
                        'main_category_id' => $categoryId,
                        'type' => 'subsubcategory',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        } else {
            // Pour une sous-catégorie, insérer uniquement les sous-sous-catégories correspondantes
            foreach ($category->children as $subsubcat) {
                DB::table('magasin_category')->insert([
                    'magasin_id' => $magasinId,
                    'category_id' => $subsubcat->id,
                    'subcategory_id' => $categoryId,
                    'main_category_id' => $category->parent_id,
                    'type' => 'subsubcategory',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
    
    // Fonction pour insérer une sous-sous-catégorie seule
    private function insertSubsubcategory($magasinId, $categoryId)
    {
        // Trouver la sous-sous-catégorie et sa hiérarchie
        $subsubcat = Category::find($categoryId);
    
        if (!$subsubcat || !$subsubcat->parent) {
            return; // Si la sous-sous-catégorie ou son parent n'existe pas, arrêtez
        }
    
        $subcategoryId = $subsubcat->parent_id;
        $mainCategoryId = $subsubcat->parent->parent_id ?? null;
    
        // Insérer la sous-sous-catégorie seule
        DB::table('magasin_category')->insert([
            'magasin_id' => $magasinId,
            'category_id' => $categoryId,
            'subcategory_id' => $subcategoryId,
            'main_category_id' => $mainCategoryId,
            'type' => 'subsubcategory',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
