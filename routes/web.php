<?php

use App\Http\Controllers\ArticleSimilaireController;
use App\Http\Controllers\InfoArticleController;
use App\Http\Controllers\CategorieVeloController;
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
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [CategorieArticleController::class, 'index'])->name('home');


Route::get('/api/categories-accessoires/{id}/subCategories', [CategorieAccessoireController::class, 'getSubCategories']);
Route::get('/api/categories-velos/{id}/subCategories', [CategorieVeloController::class, 'getSubCategories']);
Route::get('/api/categories-accessoires/parents', [CategorieAccessoireController::class, 'getParents']);
Route::get('/api/categories-velos/parents', [CategorieVeloController::class, 'getParents']);

Route::get('/velo/{reference}', [VarianteVeloController::class, 'show'])->name('velo.show');
Route::get('/boutique/{type}/{cat_id?}/{sub_id?}/{model_id?}', [BoutiqueController::class, 'index'])
    ->name('boutique.index')
    ->where('type', 'Musculaire|Electrique');

Route::middleware('guest')->group(function () {
Route::get('/connexion', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/connexion', [AuthController::class, 'login'])->name('login.submit');


// routes/web.php
Route::get('/velo/{reference}', [InfoArticleController::class, 'show'])->name('velo.show');
Route::get('/accessoire/{reference}', [InfoArticleController::class, 'show'])->name('velo.show');

Route::get('/inscription', [AuthController::class, 'showRegisterForm'])->name('register.form');


Route::post('/inscription/send-code', [AuthController::class, 'sendVerificationCode'])->name('sendVerificationCode');
Route::get('/inscription/verification', function () {
    return view('verification');
})->name('verification.form');

Route::post('/inscription/verify', [AuthController::class, 'verifyCode'])->name('verifyCode');
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

// Route pour afficher la liste des vélos par catégorie
Route::get('/boutique/{type}/{cat_id?}/{sub_id?}/{model_id?}', [BoutiqueController::class, 'index'])
    ->name('boutique.index')
    ->where('type', 'Musculaire|Electrique|Accessoires');