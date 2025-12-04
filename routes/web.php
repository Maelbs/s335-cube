<?php

use App\Http\Controllers\ArticleSimilaireController;
use App\Http\Controllers\InfoArticleController;
use App\Http\Controllers\CategorieVeloController;
use App\Http\Controllers\CategorieAccessoireController; // J'ai ajouté ceci car il manquait l'import
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategorieArticleController;
use App\Http\Controllers\VarianteVeloController;
use App\Http\Controllers\BoutiqueController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\CartController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [CategorieArticleController::class, 'index'])->name('home');

Route::get('/api/categories-accessoires/{id}/subCategories', [CategorieAccessoireController::class, 'getSubCategories']);
Route::get('/api/categories-velos/{id}/subCategories', [CategorieVeloController::class, 'getSubCategories']);
Route::get('/api/categories-accessoires/parents', [CategorieAccessoireController::class, 'getParents']);
Route::get('/api/categories-velos/parents', [CategorieVeloController::class, 'getParents']);

Route::get('/velo/{reference}', [InfoArticleController::class, 'show'])->name('velo.show');
Route::get('/accessoire/{reference}', [InfoArticleController::class, 'show'])->name('accessoire.show');

Route::middleware('guest')->group(function () {
    Route::get('/connexion', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/connexion', [AuthController::class, 'login'])->name('login.submit');

    Route::get('/inscription', [AuthController::class, 'showRegisterForm'])->name('register.form');
    Route::post('/inscription', [AuthController::class, 'checkInscription'])->name('register.check');

    Route::get('/facturation', [AuthController::class, 'showFacturationForm'])->name('facturation.form');
    Route::post('/facturation', [AuthController::class, 'sendVerificationCode'])->name('facturation.send');

    Route::get('/verification', function() { return view('verification'); })->name('verification.form');
    Route::post('/verification', [AuthController::class, 'verifyCode'])->name('verification.check');
});

Route::middleware('auth')->group(function () {
    Route::get('/profil', [ProfilController::class, 'index'])->name('profil');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

Route::get('/panier', [CartController::class, 'index'])->name('cart.index');
Route::post('/panier/ajouter/{reference}', [CartController::class, 'add'])->name('cart.add');
Route::delete('/panier/supprimer/{key}', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/panier/update', [CartController::class, 'updateQuantity'])->name('cart.update');
Route::post('/panier/ajouter-accessoire/{reference}', [CartController::class, 'addAccessoire'])->name('cart.addAccessoire');

Route::get('/boutique/{type}/{cat_id?}/{sub_id?}/{model_id?}', [BoutiqueController::class, 'index'])
    ->name('boutique.index')
    ->where('type', 'Musculaire|Electrique|Accessoires');