<?php

use App\Http\Controllers\CategorieVeloController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategorieAccessoireController;
use App\Http\Controllers\VarianteVeloController;



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

// routes/web.php
Route::get('/velo/{reference}', [VarianteVeloController::class, 'show']);
