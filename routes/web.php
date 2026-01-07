<?php

use Illuminate\Support\Facades\Route;
use App\Models\Visit; // Pour les stats admin

// Controllers
use App\Http\Controllers\CommercialController;
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
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\ProfileCompletionController;

/* ------------------------------------------------------ */
/* ROUTES PUBLIQUES                                       */
/* ------------------------------------------------------ */

Route::get('/', [CategorieArticleController::class, 'index'])->name('home');

Route::get('/boutique/{type}/{cat_id?}/{sub_id?}/{model_id?}', [BoutiqueController::class, 'index'])
    ->name('boutique.index')
    ->where('type', 'Musculaire|Electrique|Accessoires');

// Aide et Contact
Route::view('/aide', 'aide')->name('aide');
Route::view('/politique-confidentialite', 'privacy-policy')->name('privacy.policy');
Route::get('/contact', [ContactController::class, 'show'])->name('contact');
Route::post('/contact', [ContactController::class, 'submit'])->name('contact.submit');

// Authentification Google (Accessible sans être connecté)
Route::get('auth/google', [GoogleAuthController::class, 'redirect'])->name('login.google');
Route::get('auth/google/callback', [GoogleAuthController::class, 'callback']);

// API / AJAX (Pour les menus déroulants)
Route::get('/api/categories-accessoires/{id}/subCategories', [CategorieAccessoireController::class, 'getSubCategories']);
Route::get('/api/categories-velos/{id}/subCategories', [CategorieVeloController::class, 'getSubCategories']);
Route::get('/api/categories-accessoires/parents', [CategorieAccessoireController::class, 'getParents']);
Route::get('/api/categories-velos/parents', [CategorieVeloController::class, 'getParents']);

// Fiches produits & Magasins
Route::get('/velo/{reference}', [InfoArticleController::class, 'show'])->name('velo.show');
Route::get('/accessoire/{reference}', [InfoArticleController::class, 'show'])->name('accessoire.show');
Route::post('/choisir-magasin', [MagasinController::class, 'definirMagasin'])->name('magasin.definir');

/* ------------------------------------------------------ */
/* PANIER                                                 */
/* ------------------------------------------------------ */

Route::get('/panier', [PanierController::class, 'index'])->name('cart.index');
Route::post('/panier/ajouter/{reference}', [PanierController::class, 'add'])->name('cart.add');
Route::delete('/panier/supprimer/{key}', [PanierController::class, 'remove'])->name('cart.remove');
Route::post('/panier/update', [PanierController::class, 'updateQuantity'])->name('cart.update');
Route::post('/panier/ajouter-accessoire/{reference}', [PanierController::class, 'addAccessoire'])->name('cart.addAccessoire');
Route::post('/panier/apply-promo', [PanierController::class, 'applyPromo'])->name('cart.applyPromo');
Route::post('/panier/remove-promo', [PanierController::class, 'removePromo'])->name('cart.removePromo');


/* ------------------------------------------------------ */
/* INVITÉ (GUEST) - Accessible uniquement si non connecté */
/* ------------------------------------------------------ */

Route::middleware('guest')->group(function () {
    Route::get('/connexion', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/connexion', [AuthController::class, 'login'])->name('login.submit');

    Route::get('/mot-de-passe-oublie', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('/mot-de-passe-oublie', [AuthController::class, 'sendResetLinkEmail'])->name('password.email');

    Route::get('/reinitialisation-mot-de-passe/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reinitialisation-mot-de-passe', [AuthController::class, 'resetPassword'])->name('password.update');

    Route::get('/inscription', [AuthController::class, 'showRegisterForm'])->name('register.form');
    Route::post('/inscription', [AuthController::class, 'checkInscription'])->name('register.check');

    Route::get('/facturation', [AuthController::class, 'showFacturationForm'])->name('facturation.form');
    Route::post('/facturation', [AuthController::class, 'sendVerificationCode'])->name('facturation.send');

    Route::get('/verification', function() { return view('verification'); })->name('verification.form');
    Route::post('/verification', [AuthController::class, 'verifyCode'])->name('verification.check');
});

/* ------------------------------------------------------ */
/* CONNECTÉ (AUTH) - Accessible uniquement si connecté    */
/* ------------------------------------------------------ */

Route::middleware('auth')->group(function () {
    
    // Finalisation inscription (Google ou incomplet)
    Route::get('/finaliser-inscription', [ProfileCompletionController::class, 'showForm'])->name('client.complete_profile');
    Route::post('/finaliser-inscription', [ProfileCompletionController::class, 'saveDetails'])->name('client.save_profile');

    // Profil & Logout
    Route::get('/profil', [ProfilController::class, 'profil'])->name('profil');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::delete('/profil/destroy', [ProfilController::class, 'destroy'])->name('profil.destroy');
    Route::get('/profil/modifier', [ProfilController::class, 'showUpdateForm'])->name('profil.update.form');
    Route::put('/profil/modifier', [ProfilController::class, 'update'])->name('profil.update'); 

    // Commandes
    Route::get('/commandes', [CommandeController::class, 'index'])->name('client.commandes');
    Route::get('/commandes/{id}', [CommandeController::class, 'show'])->name('client.commandes.show');
    Route::get('/commandes/{id}/facture', [CommandeController::class, 'downloadInvoice'])->name('client.commandes.facture');

    // Adresses
    Route::get('/adresses', [AdresseController::class, 'index'])->name('client.adresses');
    Route::get('/adresses/create', [AdresseController::class, 'createAdresse'])->name('adresses.create.show');
    Route::post('/adresses/create/Nouvelle-Adresse', [AdresseController::class, 'create'])->name('adresses.create');
    Route::get('/adresses/{id}/edit', [AdresseController::class, 'editAdresse'])->name('adresses.edit');
    Route::post('/adresses/{id}/update', [AdresseController::class, 'update'])->name('adresses.update');

    // Paiement (Vérifie d'abord que l'adresse est remplie)
    Route::middleware(['ensure.client.address'])->group(function () {
        Route::get('payment', [PaymentController::class, 'paymentShow'])->name('payment.show');
        
        Route::post('stripe/payment', [PaymentController::class, 'paymentStripe'])->name('stripe.payment');
        Route::get('stripe/success', [PaymentController::class, 'successStripe'])->name('stripe.success');
        Route::get('stripe/cancel', [PaymentController::class, 'cancelPayment'])->name('stripe.cancel');
        
        Route::post('paypal/payment', [PaymentController::class, 'paymentPaypal'])->name('paypal.payment');    
        Route::get('paypal/success', [PaymentController::class, 'successPaypal'])->name('paypal.success');
        Route::get('paypal/cancel', [PaymentController::class, 'cancelPayment'])->name('paypal.cancel');
    });

    /* -------------------------------------------------- */
    /* ESPACE COMMERCIAL                                  */
    /* -------------------------------------------------- */
    
    Route::middleware(['commercial'])->group(function () {
        Route::get('/commercial/dashboard', [CommercialController::class, 'dashboard'])->name('commercial.dashboard');

        // Articles
        Route::get('/commercial/modifier-article', [CommercialController::class, 'articleList'])->name('commercial.edit.article');
        Route::get('/commercial/modifier-article/{reference}', [CommercialController::class, 'editArticle'])->name('commercial.article.edit');
        Route::put('/commercial/modifier-article/{reference}', [CommercialController::class, 'updateArticle'])->name('commercial.article.update');
        Route::delete('/commercial/article/{reference}', [CommercialController::class, 'destroy'])->name('commercial.article.destroy');

        // Ajouts (Catégories, Modèles, Vélos)
        Route::get('/commercial/ajouter-categorie', [CommercialController::class, 'addCategorie'])->name('commercial.add.categorie');
        Route::post('/commercial/ajouter-categorie', [CommercialController::class, 'storeCategorie'])->name('commercial.store.categorie');

        Route::get('/commercial/ajouter-modele', [CommercialController::class, 'addModele'])->name('commercial.add.modele');
        Route::post('/commercial/ajouter-modele', [CommercialController::class, 'storeModele'])->name('commercial.store.modele');
            
        Route::get('/commercial/ajouter-velo', [CommercialController::class, 'addVelo'])->name('commercial.add.velo');
        Route::post('/commercial/ajouter-velo', [CommercialController::class, 'storeVelo'])->name('commercial.store.velo');

        // Images & Caractéristiques
        Route::get('/commercial/choix-ajouter-image-modele', [CommercialController::class, 'articleListImage'])->name('commercial.choix.imageModele');
        Route::get('/commercial/ajouter-image-modele/{reference}', [CommercialController::class, 'addImageModele'])->name('commercial.add.imageModele');
        Route::post('/commercial/ajouter-image-modele', [CommercialController::class, 'storeImageModele'])->name('commercial.store.imageModele');

        Route::get('/commercial/choix-caracteristiques', [CommercialController::class, 'articleListCaracteristique'])->name('commercial.choix.caracteristique');
        Route::get('/commercial/ajouter-caracteristique/{reference}', [CommercialController::class, 'addCaracteristique'])->name('commercial.add.caracteristique');
        Route::post('/commercial/store-caracteristique', [CommercialController::class, 'storeCaracteristique'])->name('commercial.store.caracteristique');
    });
});

/* ------------------------------------------------------ */
/* STATS ADMIN (SÉCURISÉ PAR .ENV)                        */
/* ------------------------------------------------------ */

Route::get('/admin-logs', function () {
    // 1. SÉCURITÉ : On lit les valeurs du fichier .env
    $login = env('LOGS_ADMIN_USER', 'admin'); 
    $password = env('LOGS_ADMIN_PASSWORD'); 

    // Sécurité supplémentaire : Si pas de mot de passe configuré, on bloque tout
    if (!$password) {
        abort(403, 'Configuration de sécurité manquante.');
    }

    // 2. VÉRIFICATION BASIC AUTH
    if (request()->getUser() !== $login || request()->getPassword() !== $password) {
        return response('Accès refusé', 401, ['WWW-Authenticate' => 'Basic']);
    }

    // 3. AFFICHAGE DES STATS
    $total = Visit::count();
    $visits = Visit::orderBy('visited_at', 'desc')->take(100)->get();

    return view('admin_stats', compact('total', 'visits'));
});