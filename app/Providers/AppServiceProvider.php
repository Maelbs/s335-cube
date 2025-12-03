<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Session; // <--- AJOUT IMPORTANT
use App\Models\CategorieAccessoire;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Le '*' signifie que ces données sont partagées avec TOUTES les vues du site
        View::composer('*', function ($view) {
            
            // --- 1. TON CODE EXISTANT (MENU ACCESSOIRES) ---
            $accessoiresMenu = CategorieAccessoire::whereNull('cat_id_categorie_accessoire')
                ->with('enfants.enfants')
                ->get();
                
            $view->with('categorieAccessoires', $accessoiresMenu);


            // --- 2. LE CODE POUR LE PANIER (AJOUT) ---
            
            // Récupère le panier de la session (tableau vide si inexistant)
            $cartItems = Session::get('cart', []);
            $cartTotal = 0;

            // Calcul du total (Prix * Quantité) pour chaque article
            if (is_array($cartItems)) {
                foreach ($cartItems as $item) {
                    // Vérification de sécurité : tableau ou objet ?
                    $price = is_array($item) ? ($item['price'] ?? 0) : ($item->price ?? 0);
                    $qty = is_array($item) ? ($item['quantity'] ?? 1) : ($item->quantity ?? 1);
                    
                    $cartTotal += $price * $qty;
                }
            }

            // On envoie les variables $cartItems et $cartTotal à la vue
            $view->with('cartItems', $cartItems);
            $view->with('cartTotal', $cartTotal);
        });
    }
}