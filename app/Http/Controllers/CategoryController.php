<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Magasin;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        // Charger les sous-catégories et sous-sous-catégories avec les images
        $categories = Category::with(['children.children' => function ($query) {
            // Charger uniquement les colonnes nécessaires pour les sous-sous-catégories
            $query->select('id', 'name', 'parent_id', 'image');
        }])
        ->whereNull('parent_id') // Récupérer uniquement les catégories principales
        ->select('id', 'name', 'image') // Inclure l'image des catégories principales
        ->get();
    
        return response()->json($categories);
    }
    


    // Récupérer les sous-catégories d'une catégorie donnée
    public function getSubCategories(Category $category)
    {
        $subCategories = $category->children()->get();
        return response()->json($subCategories);
    }

  

    public function searchMagasinsByCategory($categoryId, Request $request)
    {
        $location = $request->input('location'); // Récupérer la localisation envoyée depuis la requête
    
        $magasins = Magasin::whereHas('categories', function($query) use ($categoryId) {
            $query->where('category_id', $categoryId)
                  ->orWhere('subcategory_id', $categoryId)
                  ->orWhere('main_category_id', $categoryId);
        });
    
        // Ajouter un filtre sur la localisation si elle est fournie
        if ($location) {
            $magasins->where('adresse', 'LIKE', '%' . $location . '%');
        }
    
        $magasins = $magasins->get();
    
        if ($magasins->isEmpty()) {
            // Retourner un statut 200 avec un message informant qu'aucun magasin n'a été trouvé
            return response()->json(['message' => 'Aucun magasin trouvé pour cette catégorie.', 'magasins' => []], 200);
        }
    
        return response()->json($magasins);
    }
    


public function getSubSubCategories($subCategoryId)
{
    // Récupérer les sous-sous-catégories dont le parent est la sous-catégorie sélectionnée
    $subSubCategories = Category::where('parent_id', $subCategoryId)->get();

    return response()->json($subSubCategories);
}


public function uploadImage(Request $request, $id)
{
    $category = Category::find($id);

    if ($request->hasFile('image')) {
        $image = $request->file('image');
        $imageName = time() . '_' . $image->getClientOriginalName();
        $imagePath = $image->storeAs('categories', $imageName, 'public');

        // Sauvegarder le chemin de l'image dans la catégorie (sous-sous-catégorie)
        $category->image = '/storage/' . $imagePath; // Assurez-vous que le chemin est correct
        $category->save();

        return response()->json(['message' => 'Image upload successful', 'path' => $category->image]);
    }

    return response()->json(['message' => 'No image uploaded'], 400);
}




}

