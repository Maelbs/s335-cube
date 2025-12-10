<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ArticleSimilaireController;
use App\Http\Controllers\InfoArticleController;
use App\Http\Controllers\CategorieVeloController;
use App\Http\Controllers\CategorieAccessoireController; 
use App\Http\Controllers\CategorieArticleController;
use App\Http\Controllers\VarianteVeloController;
use App\Http\Controllers\BoutiqueController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\PanierController;
use App\Http\Controllers\PaymentController;

use App\Http\Controllers\CommandeController;

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
    Route::get('/profil', [ProfilController::class, 'profil'])->name('profil');
    Route::get('/commandes', [CommandeController::class, 'index'])->name('client.commandes');
    Route::get('/commandes/{id}', [CommandeController::class, 'show'])->name('client.commandes.show');
    Route::get('/commandes/{id}/facture', [CommandeController::class, 'downloadInvoice'])->name('client.commandes.facture');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/profil/modifier', [ProfilController::class, 'showUpdateForm'])->name('profil.update.form');
    Route::put('/profil/modifier', [ProfilController::class, 'update'])->name('profil.update'); 

    Route::get('paypal/payment', [PaymentController::class, 'paymentPaypal'])->name('paypal.payment');
    Route::get('paypal/success', [PaymentController::class, 'successPaypal'])->name('paypal.success');
    Route::get('paypal/cancel', [PaymentController::class, 'cancelPaypal'])->name('paypal.cancel');
});

Route::get('/panier', [PanierController::class, 'index'])->name('cart.index');
Route::post('/panier/ajouter/{reference}', [PanierController::class, 'add'])->name('cart.add');
Route::delete('/panier/supprimer/{key}', [PanierController::class, 'remove'])->name('cart.remove');
Route::post('/panier/update', [PanierController::class, 'updateQuantity'])->name('cart.update');
Route::post('/panier/ajouter-accessoire/{reference}', [PanierController::class, 'addAccessoire'])->name('cart.addAccessoire');

Route::get('/boutique/{type}/{cat_id?}/{sub_id?}/{model_id?}', [BoutiqueController::class, 'index'])
    ->name('boutique.index')
    ->where('type', 'Musculaire|Electrique|Accessoires');

