<?php

use App\Http\Controllers\CategoryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MagasinController;


//CategoryController
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{id}/magasins-by-main', [CategoryController::class, 'magasinsByMainCategory']);
Route::get('/categories/{category}/subcategories', [CategoryController::class, 'getSubCategories']);
Route::get('/categories/{subcategory}/subsubcategories', [CategoryController::class, 'getSubSubCategories']);
Route::get('/categories/{id}/magasins', [CategoryController::class, 'searchMagasinsByCategory']);
Route::post('/categories/{id}/upload-image', [CategoryController::class, 'uploadImage']);

//MagasinController
Route::get('/magasins', [MagasinController::class, 'index']);
Route::post('/magasins/{magasin}/categories', [MagasinController::class, 'addCategory']);
Route::get('/magasins/{magasin}/categories', [MagasinController::class, 'getCategories']);
Route::post('/magasins', [MagasinController::class, 'searchMagasinsByCategoryAndLocation']);
Route::post('/magasins/nearby', [MagasinController::class, 'getMagasinsAutourDeMoi']);
Route::post('/magasins/search-by-radius', [MagasinController::class, 'searchMagasinsByRadius']);
Route::post('/magasins/{id}/upload-image', [MagasinController::class, 'uploadImage']);

Route::delete('/magasins/{magasin}/categories/{subcategory}/remove-subcategory', [MagasinController::class, 'removeSubCategory']);
Route::delete('/magasins/{magasin}/categories/{category}/remove-subsub-category', [MagasinController::class, 'removeSubSubCategory']);
Route::delete('/magasins/{magasin}/categories/{mainCategory}/remove-main-category', [MagasinController::class, 'removeMainCategory']);

