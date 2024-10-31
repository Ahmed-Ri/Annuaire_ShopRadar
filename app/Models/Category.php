<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    // Spécification que l'ID est une chaîne de caractères
    protected $keyType = 'string';
    
    // Désactiver l'auto-incrémentation
    public $incrementing = false;

    // Définir les attributs pouvant être remplis
    protected $fillable = ['id', 'name', 'image', 'parent_id'];

    // Relation : une catégorie peut avoir plusieurs sous-catégories (enfants)
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    // Relation : une catégorie peut appartenir à une autre catégorie (parent)
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    // Relation : une catégorie peut être liée à plusieurs magasins via la table pivot
    public function magasins()
    {
        return $this->belongsToMany(Magasin::class, 'magasin_category')
                    ->withPivot('type', 'subcategory_id', 'main_category_id')
                    ->withTimestamps();
    }
}
