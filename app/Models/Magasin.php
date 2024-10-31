<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Magasin extends Model
{
    // Spécification que l'ID est une chaîne de caractères
    protected $keyType = 'string';
    
    // Désactiver l'auto-incrémentation
    public $incrementing = false;

    // Définir les attributs pouvant être remplis
    protected $fillable = ['id', 'nom','ACTIVITE','adresse', 'tel', 'IDE', 'image', 'AdresseCourte','CP','VILLE','TEFET','LIBTEFET','SIRET','MOBILE','EMAIL'];

    // Relation : un magasin peut être lié à plusieurs catégories via la table pivot
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'magasin_category')
                    ->withPivot('type', 'subcategory_id', 'main_category_id')
                    ->withTimestamps();
    }

    // Méthode pour attacher une catégorie à un magasin en tenant compte de la hiérarchie
    public function attachCategory($category)
{
    $subcategoryId = $category->parent ? $category->parent->id : null;
    $mainCategoryId = $category->parent && $category->parent->parent ? $category->parent->parent->id : null;

    // Assurez-vous que main_category_id n'est pas null avant d'insérer
    if ($mainCategoryId !== null) {
        $this->categories()->attach($category->id, [
            'type' => $this->determineCategoryType($category),
            'subcategory_id' => $subcategoryId,
            'main_category_id' => $mainCategoryId,
        ]);
    } else {
        Log::warning('Tentative d\'insertion avec main_category_id null pour la catégorie : ' . $category->id);
    }
}


    // Méthode pour déterminer le type de catégorie (catégorie principale, sous-catégorie, sous-sous-catégorie)
    private function determineCategoryType($category)
    {
        if ($category->parent_id === null) {
            return 'category'; // Catégorie principale
        } elseif ($category->parent && $category->parent->parent_id === null) {
            return 'subcategory'; // Sous-catégorie
        } else {
            return 'subsubcategory'; // Sous-sous-catégorie
        }
    }
}

