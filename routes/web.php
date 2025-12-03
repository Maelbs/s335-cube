<?php

use App\Http\Controllers\ArticleSimilaireController;
use App\Http\Controllers\InfoArticleController;
use App\Http\Controllers\CategorieVeloController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategorieArticleController;
use App\Http\Controllers\VarianteVeloController;
use App\Http\Controllers\BoutiqueController;
use App\Http\Controllers\CartController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [CategorieArticleController::class, 'index'])->name('home');

// Route::get('/api/categories-accessoires/{id}/subCategories', [CategorieAccessoireController::class, 'getSubCategories']);
// Route::get('/api/categories-velos/{id}/subCategories', [CategorieVeloController::class, 'getSubCategories']);

Route::get('/api/categories-accessoires/parents', [CategorieAccessoireController::class, 'getParents']);
Route::get('/api/categories-velos/parents', [CategorieVeloController::class, 'getParents']);

// routes/web.php
Route::get('/velo/{reference}', [InfoArticleController::class, 'show'])->name('velo.show');
Route::get('/accessoire/{reference}', [InfoArticleController::class, 'show'])->name('velo.show');


Route::get('/panier', [CartController::class, 'index'])->name('cart.index');
Route::post('/panier/ajouter/{reference}', [CartController::class, 'add'])->name('cart.add');
Route::delete('/panier/supprimer/{key}', [CartController::class, 'remove'])->name('cart.remove');


// Route pour afficher la liste des vélos par catégorie
Route::get('/boutique/{type}/{cat_id?}/{sub_id?}/{model_id?}', [BoutiqueController::class, 'index'])
    ->name('boutique.index')
    ->where('type', 'Musculaire|Electrique');
