<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ImportController extends Controller
{
    public function importCategoriesFromCsv()
    {
        $filePath = storage_path('app/categories.csv');

        if (!file_exists($filePath)) {
            return response()->json(['error' => 'Le fichier CSV est introuvable.'], 404);
        }

        $fileContent = file_get_contents($filePath);
        $fileContent = preg_replace('/^\x{FEFF}/u', '', $fileContent);
        $csvData = array_map('str_getcsv', explode("\n", $fileContent));

        $headers = array_shift($csvData);

        foreach ($csvData as $row) {
            if (count($row) !== count($headers)) {
                continue;
            }

            $rowData = array_combine($headers, $row);

            if (!isset($rowData['magasin_id']) || !isset($rowData['category_id'])) {
                continue;
            }

            $magasinId = $rowData['magasin_id'];
            $categoryId = $rowData['category_id'];

            // Appeler la méthode qui gère l'insertion de la hiérarchie
            $this->insertCategoryHierarchyWithoutSelf($magasinId, $categoryId);
        }

        return response()->json(['message' => 'Importation terminée avec succès']);
    }

    private function insertCategoryHierarchyWithoutSelf($magasinId, $categoryId)
    {
        $category = Category::find($categoryId);

        if (!$category) {
            return;
        }

        // Cas 1: Si c'est une catégorie principale, insérer uniquement ses sous-catégories et sous-sous-catégories
        if ($category->parent_id === null) {
            $this->insertDescendants($magasinId, $category);
        }
        // Cas 2: Si c'est une sous-catégorie, insérer uniquement les sous-sous-catégories sans insérer la sous-catégorie elle-même ni la catégorie principale
        elseif ($category->parent && $category->parent->parent_id === null) {
            // Insérer uniquement les sous-sous-catégories de cette sous-catégorie
            foreach ($category->children as $subsubcat) {
                $this->insertCategoryWithType($magasinId, $subsubcat, 'subsubcategory');
            }
        }
        // Cas 3: Si c'est une sous-sous-catégorie, insérer uniquement elle-même
        else {
            $this->insertCategoryWithType($magasinId, $category, 'subsubcategory');
        }
    }

    private function insertDescendants($magasinId, $category)
    {
        // Gère les descendants des catégories principales seulement (sous-catégories et sous-sous-catégories)
        foreach ($category->children as $subcat) {
            foreach ($subcat->children as $subsubcat) {
                $this->insertCategoryWithType($magasinId, $subsubcat, 'subsubcategory');
            }
        }
    }

    private function insertCategoryWithType($magasinId, $category, $type)
    {
        $subcategoryId = null;
        $mainCategoryId = null;

        if ($type === 'category') {
            $mainCategoryId = $category->id;
        } elseif ($type === 'subcategory') {
            $subcategoryId = $category->id;
            $mainCategoryId = $category->parent_id;
        } else {
            $subcategoryId = $category->parent_id;
            $mainCategoryId = $category->parent->parent_id ?? null;
        }

        // Vérifie si l'enregistrement existe déjà pour éviter les doublons
        if (!$this->entryExists($magasinId, $category->id, $subcategoryId, $mainCategoryId, $type)) {
            // Insertion dans la table magasin_category
            DB::table('magasin_category')->insert([
                'magasin_id' => $magasinId,
                'category_id' => $category->id,
                'subcategory_id' => $subcategoryId,
                'main_category_id' => $mainCategoryId,
                'type' => $type,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    private function entryExists($magasinId, $categoryId, $subcategoryId, $mainCategoryId, $type)
    {
        return DB::table('magasin_category')
            ->where('magasin_id', $magasinId)
            ->where('category_id', $categoryId)
            ->where('subcategory_id', $subcategoryId)
            ->where('main_category_id', $mainCategoryId)
            ->where('type', $type)
            ->exists();
    }
}
