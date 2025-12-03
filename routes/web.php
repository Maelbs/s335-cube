<?php

use App\Http\Controllers\ArticleSimilaireController;
use App\Http\Controllers\CategorieVeloController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategorieAccessoireController;
use App\Http\Controllers\VarianteVeloController;
use App\Http\Controllers\BoutiqueController;
use App\Http\Controllers\AuthController;  
use App\Http\Controllers\ProfilController;



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

Route::get('/', [CategorieVeloController::class, 'index'])->name('home');

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
