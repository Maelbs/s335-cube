<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\View;
use App\Models\CategorieVelo;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function __construct()
    {
        // On entoure d'un try-catch pour éviter les erreurs si la BDD n'est pas encore migrée
        try {
            // --- 1. MENU VÉLOS MUSCULAIRES ---
            // On cherche les Catégories Racines...
            $menuVelo = CategorieVelo::whereNull('cat_id_categorie')
                // ...qui contiennent des sous-catégories ayant des modèles 'musculaire'
                ->whereHas('enfants.modeles', function ($q) {
                    $q->where('type_velo', 'musculaire');
                })
                // On charge les Enfants et leurs Modèles, mais uniquement les 'musculaire'
                ->with(['enfants' => function ($q) {
                    $q->whereHas('modeles', function ($q2) {
                        $q2->where('type_velo', 'musculaire');
                    })
                    ->with(['modeles' => function ($q3) {
                        $q3->where('type_velo', 'musculaire');
                    }]);
                }])
                ->get();

            // --- 2. MENU VÉLOS ÉLECTRIQUES ---
            // Attention à l'orthographe 'eletrique' (sans 'c') selon votre base de données
            $menuElec = CategorieVelo::whereNull('cat_id_categorie')
                ->whereHas('enfants.modeles', function ($q) {
                    $q->where('type_velo', 'electrique');
                })
                ->with(['enfants' => function ($q) {
                    $q->whereHas('modeles', function ($q2) {
                        $q2->where('type_velo', 'electrique');
                    })
                    ->with(['modeles' => function ($q3) {
                        $q3->where('type_velo', 'electrique');
                    }]);
                }])
                ->get();

            // --- 3. PARTAGE AVEC TOUTES LES VUES ---
            // Ces variables seront désormais accessibles dans header.blade.php sur TOUTES les pages
            View::share('menuVelo', $menuVelo);
            View::share('menuElec', $menuElec);

        } catch (\Exception $e) {
            // En cas d'erreur (ex: table inexistante), on ne fait rien pour ne pas bloquer l'app
        }
    }
}