<?php

use App\Http\Controllers\CommercialController;
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
use App\Http\Controllers\MagasinController;
use App\Http\Controllers\CommandeController;
use App\Http\Controllers\AdresseController;


use App\Http\Controllers\ContactController; 

/* ... TOUTES VOS AUTRES ROUTES ... */

// Route boutique
Route::get('/boutique/{type}/{cat_id?}/{sub_id?}/{model_id?}', [BoutiqueController::class, 'index'])
    ->name('boutique.index')
    ->where('type', 'Musculaire|Electrique|Accessoires');


// ------------------------------------
// PARTIE AIDE ET CONTACT
// ------------------------------------

Route::view('/aide', 'aide')->name('aide');
Route::view('/politique-confidentialite', 'privacy-policy')->name('privacy.policy');

Route::get('/contact', [ContactController::class, 'show'])->name('contact');
Route::post('/contact', [ContactController::class, 'submit'])->name('contact.submit');

Route::get('/', [CategorieArticleController::class, 'index'])->name('home');

Route::get('/api/categories-accessoires/{id}/subCategories', [CategorieAccessoireController::class, 'getSubCategories']);
Route::get('/api/categories-velos/{id}/subCategories', [CategorieVeloController::class, 'getSubCategories']);
Route::get('/api/categories-accessoires/parents', [CategorieAccessoireController::class, 'getParents']);
Route::get('/api/categories-velos/parents', [CategorieVeloController::class, 'getParents']);

Route::get('/velo/{reference}', [InfoArticleController::class, 'show'])->name('velo.show');
Route::get('/accessoire/{reference}', [InfoArticleController::class, 'show'])->name('accessoire.show');
Route::post('/choisir-magasin', [MagasinController::class, 'definirMagasin'])->name('magasin.definir');

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

    Route::get('/adresses', [AdresseController::class, 'index'])->name('client.adresses');
    Route::get('/adresses/create', [AdresseController::class, 'createAdresse'])->name('adresses.create.show');
    Route::post('/adresses/create/Nouvelle-Adresse', [AdresseController::class, 'create'])->name('adresses.create');
    Route::get('/adresses/{id}/edit', [AdresseController::class, 'editAdresse'])->name('adresses.edit');
    Route::post('/adresses/{id}/update', [AdresseController::class, 'update'])->name('adresses.update');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::delete('/profil/destroy', [ProfilController::class, 'destroy'])->name('profil.destroy');

    Route::get('/profil/modifier', [ProfilController::class, 'showUpdateForm'])->name('profil.update.form');
    Route::put('/profil/modifier', [ProfilController::class, 'update'])->name('profil.update'); 

    Route::get('payment', [PaymentController::class, 'paymentShow'])->name('payment.show');
    Route::post('stripe/payment', [PaymentController::class, 'paymentStripe'])->name('stripe.payment');
    Route::get('stripe/success', [PaymentController::class, 'successStripe'])->name('stripe.success');
    Route::get('stripe/cancel', [PaymentController::class, 'cancelPayment'])->name('stripe.cancel');
    Route::post('paypal/payment', [PaymentController::class, 'paymentPaypal'])->name('paypal.payment');    
    Route::get('paypal/success', [PaymentController::class, 'successPaypal'])->name('paypal.success');
    Route::get('paypal/cancel', [PaymentController::class, 'cancelPayment'])->name('paypal.cancel');


    Route::middleware(['commercial'])->group(function () {
        Route::get('/commercial/dashboard', [CommercialController::class, 'dashboard'])
             ->name('commercial.dashboard');

             // Dans le groupe middleware('commercial') ...

        Route::get('/commercial/modifier-article', [CommercialController::class, 'articleList'])->name('commercial.edit.article');
            // Formulaire de modification (Affichage)
            Route::get('/commercial/modifier-article/{reference}', [CommercialController::class, 'editArticle'])
                ->name('commercial.article.edit');

            // Traitement de la modification (Mise à jour BDD)
            Route::put('/commercial/modifier-article/{reference}', [CommercialController::class, 'updateArticle'])
                ->name('commercial.article.update');
                
            Route::delete('/commercial/article/{reference}', [CommercialController::class, 'destroy'])
                ->name('commercial.article.destroy');

        // Affichage du formulaire
        Route::get('/commercial/ajouter-categorie', [CommercialController::class, 'addCategorie'])
            ->name('commercial.add.categorie');

        // Traitement du formulaire
        Route::post('/commercial/ajouter-categorie', [CommercialController::class, 'storeCategorie'])
            ->name('commercial.store.categorie');

        Route::get('/commercial/ajouter-modele', [CommercialController::class, 'addModele'])
            ->name('commercial.add.modele');

        Route::post('/commercial/ajouter-modele', [CommercialController::class, 'storeModele'])
            ->name('commercial.store.modele');
            
        Route::get('/commercial/ajouter-velo', [CommercialController::class, 'addVelo'])
            ->name('commercial.add.velo');

        Route::post('/commercial/ajouter-velo', [CommercialController::class, 'storeVelo'])
            ->name('commercial.store.velo');

        Route::get('/commercial/choix-ajouter-image-modele', [CommercialController::class, 'articleListImage'])
            ->name('commercial.choix.imageModele');

        Route::get('/commercial/ajouter-image-modele/{reference}', [CommercialController::class, 'addImageModele'])
            ->name('commercial.add.imageModele');

        Route::post('/commercial/ajouter-image-modele', [CommercialController::class, 'storeImageModele'])
            ->name('commercial.store.imageModele');

        Route::get('/commercial/choix-caracteristiques', [CommercialController::class, 'articleListCaracteristique'])
            ->name('commercial.choix.caracteristique');

        Route::get('/commercial/ajouter-caracteristique/{reference}', [CommercialController::class, 'addCaracteristique'])
            ->name('commercial.add.caracteristique');

        Route::post('/commercial/store-caracteristique', [CommercialController::class, 'storeCaracteristique'])
            ->name('commercial.store.caracteristique');
    });
});

Route::get('/panier', [PanierController::class, 'index'])->name('cart.index');
Route::post('/panier/ajouter/{reference}', [PanierController::class, 'add'])->name('cart.add');
Route::delete('/panier/supprimer/{key}', [PanierController::class, 'remove'])->name('cart.remove');
Route::post('/panier/update', [PanierController::class, 'updateQuantity'])->name('cart.update');
Route::post('/panier/ajouter-accessoire/{reference}', [PanierController::class, 'addAccessoire'])->name('cart.addAccessoire');
Route::post('/panier/apply-promo', [PanierController::class, 'applyPromo'])->name('cart.applyPromo');
Route::post('/panier/remove-promo', [PanierController::class, 'removePromo'])->name('cart.removePromo');

Route::get('/boutique/{type}/{cat_id?}/{sub_id?}/{model_id?}', [BoutiqueController::class, 'index'])
    ->name('boutique.index')
    ->where('type', 'Musculaire|Electrique|Accessoires');



Route::view('/aide', 'aide')->name('aide');