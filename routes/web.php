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

// APIs
Route::get('/api/categories-accessoires/{id}/subCategories', [CategorieAccessoireController::class, 'getSubCategories']);
Route::get('/api/categories-velos/{id}/subCategories', [CategorieVeloController::class, 'getSubCategories']);
Route::get('/api/categories-accessoires/parents', [CategorieAccessoireController::class, 'getParents']);
Route::get('/api/categories-velos/parents', [CategorieVeloController::class, 'getParents']);


// --- CORRECTION ICI : CES ROUTES SONT MAINTENANT ACCESSIBLES À TOUS ---

// Note : Vous aviez deux routes pour '/velo/{reference}'. J'ai gardé celle qui utilise InfoArticleController
// car c'est celle qui était groupée avec 'accessoire'. Assurez-vous que c'est le bon contrôleur.
Route::get('/velo/{reference}', [InfoArticleController::class, 'show'])->name('velo.show');

// Note : J'ai changé le nom de la route ici pour 'accessoire.show' pour éviter les doublons de noms
Route::get('/accessoire/{reference}', [InfoArticleController::class, 'show'])->name('accessoire.show');

// ----------------------------------------------------------------------


// Routes accessibles UNIQUEMENT si on n'est PAS connecté (Guest)
Route::middleware('guest')->group(function () {
    Route::get('/connexion', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/connexion', [AuthController::class, 'login'])->name('login.submit');

    Route::get('/inscription', [AuthController::class, 'showRegisterForm'])->name('register.form');
    Route::post('/inscription/send-code', [AuthController::class, 'sendVerificationCode'])->name('sendVerificationCode');
    Route::get('/inscription/verification', function () {
        return view('verification');
    })->name('verification.form');
    Route::post('/inscription/verify', [AuthController::class, 'verifyCode'])->name('verifyCode');
});

// Routes accessibles UNIQUEMENT si on EST connecté (Auth)
Route::middleware('auth')->group(function () {
    Route::get('/profil', [ProfilController::class, 'index'])->name('profil');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// Routes Panier (Accessibles à tous ou auth selon votre logique, ici accessibles à tous)
Route::get('/panier', [CartController::class, 'index'])->name('cart.index');
Route::post('/panier/ajouter/{reference}', [CartController::class, 'add'])->name('cart.add');
Route::delete('/panier/supprimer/{key}', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/panier/update', [CartController::class, 'updateQuantity'])->name('cart.update');
Route::post('/panier/ajouter-accessoire/{reference}', [CartController::class, 'addAccessoire'])->name('cart.addAccessoire');

// Boutique (Note: Vous aviez défini cette route deux fois, j'ai gardé la version la plus complète à la fin)
Route::get('/boutique/{type}/{cat_id?}/{sub_id?}/{model_id?}', [BoutiqueController::class, 'index'])
    ->name('boutique.index')
    ->where('type', 'Musculaire|Electrique|Accessoires');