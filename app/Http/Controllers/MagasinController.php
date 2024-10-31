<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\Magasin;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;



class MagasinController extends Controller
{
    public function index()
{
    $magasins = Magasin::all();
    return response()->json($magasins);
}

public function getCategories(Magasin $magasin)
{
    $categories = DB::table('magasin_category')
        ->where('magasin_id', $magasin->id)
        // Joindre les catégories principales
        ->join('categories as main', 'magasin_category.main_category_id', '=', 'main.id')
        // Joindre les sous-catégories (si disponibles)
        ->leftJoin('categories as sub', 'magasin_category.subcategory_id', '=', 'sub.id')
        // Joindre les sous-sous-catégories (si disponibles)
        ->leftJoin('categories as cat', 'magasin_category.category_id', '=', 'cat.id')
        ->select(
            'main.name as main_category',         // Nom de la catégorie principale
            'sub.name as sub_category',           // Nom de la sous-catégorie
            'cat.name as sub_sub_category',       // Nom de la sous-sous-catégorie (précédemment 'category')
            'magasin_category.category_id',       // ID de la sous-sous-catégorie
            'magasin_category.subcategory_id',    // ID de la sous-catégorie
            'magasin_category.main_category_id'   // ID de la catégorie principale
        )
        ->get();

    // Formater les résultats pour retourner les catégories, sous-catégories et sous-sous-catégories
    $formattedCategories = [];

    foreach ($categories as $category) {
        $formattedCategories[] = [
            'main' => $category->main_category,         // Catégorie principale
            'sub' => $category->sub_category,           // Sous-catégorie
            'sub_sub' => $category->sub_sub_category,   // Sous-sous-catégorie
            'category_id' => $category->category_id,    // ID de la sous-sous-catégorie
            'subcategory_id' => $category->subcategory_id, // ID de la sous-catégorie
            'main_category_id' => $category->main_category_id, // ID de la catégorie principale
        ];
    }

    return response()->json($formattedCategories);
}



    
    
    
    public function addCategory(Request $request, Magasin $magasin)
    {
        // Valider que `category_ids` est un tableau et que chaque élément est un entier valide existant dans la table `categories`
        $validated = $request->validate([
            'category_ids' => 'required|array', // Vérifier que `category_ids` est un tableau
            'category_ids.*' => 'string|exists:categories,id', // Vérifier que chaque élément du tableau est un entier et existe dans `categories`
        ]);
    
        // Initialiser un tableau pour stocker tous les IDs de catégories à affecter
        $allCategoryIds = [];
    
        foreach ($validated['category_ids'] as $categoryId) {
            $category = Category::find($categoryId);
    
            if ($category) {
                // Si la catégorie sélectionnée est une catégorie principale (et non une sous-catégorie ou sous-sous-catégorie)
                if ($category->parent_id === null) {
                    // Si c'est une catégorie principale, récupérer toutes ses sous-catégories
                    $subCategoryIds = $category->children()->pluck('id')->toArray();
                    $allCategoryIds = array_merge($allCategoryIds, $subCategoryIds);
    
                    // Récupérer toutes les sous-sous-catégories des sous-catégories
                    $subSubCategoryIds = Category::whereIn('parent_id', $subCategoryIds)->pluck('id')->toArray();
                    $allCategoryIds = array_merge($allCategoryIds, $subSubCategoryIds);
                }
                // Si c'est une sous-catégorie, récupérer ses sous-sous-catégories
                elseif ($category->parent && $category->parent->parent_id === null) {
                    // C'est une sous-catégorie, donc on récupère toutes ses sous-sous-catégories
                    $subSubCategoryIds = $category->children()->pluck('id')->toArray();
                    $allCategoryIds = array_merge($allCategoryIds, $subSubCategoryIds);
                }
    
                // Ajouter l'ID de la catégorie elle-même
                $allCategoryIds[] = $category->id;
            }
        }
    
        // Supprimer les doublons si certaines catégories apparaissent plusieurs fois
        $allCategoryIds = array_unique($allCategoryIds);
    
        // Associer chaque catégorie unique au magasin
        foreach ($allCategoryIds as $categoryId) {
            $category = Category::find($categoryId);
            if ($category) {
                $magasin->attachCategory($category); // Associer chaque catégorie au magasin
            }
        }
    
        return response()->json(['message' => 'Catégories affectées avec succès']);
    }
    
    

    public function searchMagasinsByCategoryAndLocation(Request $request)
    {
        try {
            $categoryId = $request->input('category_id');
            $query = $request->input('query'); 
    
            // Si une catégorie est sélectionnée (ne pas modifier cette partie)
            if ($categoryId) {
                $magasins = Magasin::whereHas('categories', function($subQuery) use ($categoryId) {
                    $subQuery->where('category_id', $categoryId)
                             ->orWhere('subcategory_id', $categoryId)
                             ->orWhere('main_category_id', $categoryId);
                })->get();
            }
            // Si un mot-clé est fourni (corrigé pour rechercher dans toutes les catégories, sous-catégories, sous-sous-catégories)
            else if ($query) {
                $magasins = Magasin::whereHas('categories', function($subQuery) use ($query) {
                    // Recherche sur le nom des catégories
                    $subQuery->where('name', 'like', '%' . $query . '%')
                             ->orWhereHas('parent', function($parentQuery) use ($query) {
                                 // Rechercher dans le nom de la catégorie parent (pour sous-catégories et sous-sous-catégories)
                                 $parentQuery->where('name', 'like', '%' . $query . '%')
                                             ->orWhereHas('parent', function($grandParentQuery) use ($query) {
                                                 // Rechercher aussi dans les catégories parents des sous-catégories
                                                 $grandParentQuery->where('name', 'like', '%' . $query . '%');
                                             });
                             });
                })->get();
            }
    
            // Si aucun magasin n'est trouvé
            if ($magasins->isEmpty()) {
                return response()->json(['message' => 'Aucun magasin trouvé pour cette catégorie ou ce mot-clé.'], 404);
            }
    
            return response()->json($magasins);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la recherche des magasins: ' . $e->getMessage());
            return response()->json(['message' => 'Erreur interne du serveur.'], 500);
        }
    }
    

    

public function searchMagasinsByRadius(Request $request)
{
    $categoryId = $request->input('category_id');
    $location = $request->input('location');
    $radius = $request->input('radius'); // Rayon sélectionné

    // Logique pour calculer la distance et filtrer les magasins dans le rayon donné
    $magasins = Magasin::whereHas('categories', function($query) use ($categoryId) {
        $query->where('category_id', $categoryId)
              ->orWhere('subcategory_id', $categoryId)
              ->orWhere('main_category_id', $categoryId);
    })
    ->where('distance', '<=', $radius) // Filtrer les magasins en fonction du rayon
    ->get();

    return response()->json($magasins);
}


public function uploadImage(Request $request, $id)
{
    $magasin = Magasin::find($id);

    if ($request->hasFile('image')) {
        $image = $request->file('image');
        $imageName = time() . '_' . $image->getClientOriginalName();
        $imagePath = $image->storeAs('magasins', $imageName, 'public');

        // Sauvegarder le chemin de l'image dans le magasin
        $magasin->image = '/storage/' . $imagePath;
        $magasin->save();

        return response()->json(['message' => 'Image upload successful', 'path' => $magasin->image]);
    }

    return response()->json(['message' => 'No image uploaded'], 400);
}



public function removeSubCategory(Magasin $magasin, $subcategoryId)
{
    // Supprimer la sous-catégorie et les sous-sous-catégories associées
    DB::table('magasin_category')
        ->where('magasin_id', $magasin->id)
        ->where('subcategory_id', $subcategoryId)
        ->delete();

    return response()->json(['message' => 'Sous-catégorie et ses sous-sous-catégories supprimées avec succès']);
}

public function removeSubSubCategory(Magasin $magasin, $categoryId)
{
    // Supprimer uniquement la sous-sous-catégorie
    DB::table('magasin_category')
        ->where('magasin_id', $magasin->id)
        ->where('category_id', $categoryId)
        ->delete();

    return response()->json(['message' => 'Sous-sous-catégorie supprimée avec succès']);
}
public function removeMainCategory(Magasin $magasin, $mainCategoryId)
{
    // Supprimer toutes les associations entre le magasin et cette catégorie principale (ainsi que ses sous-catégories et sous-sous-catégories)
    DB::table('magasin_category')
        ->where('magasin_id', $magasin->id)
        ->where('main_category_id', $mainCategoryId)
        ->delete();

    return response()->json(['message' => 'Catégorie principale et ses sous-catégories supprimées avec succès']);
}

    
}