<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache; // <--- J'ai ajouté ça
use App\Models\CategorieVelo;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function __construct()
    {
        // On enveloppe tout dans un Cache::remember.
        // 'menus_header' est le nom de la clé.
        // 60*60 = 3600 secondes (1 heure). Le menu ne sera recalculé que toutes les heures.
        $menus = Cache::remember('menus_header', 60 * 60, function () {
            
            // On prépare une fonction pour éviter de copier-coller le code
            // Cela rend le code plus lisible et plus propre.
            $getMenu = function ($type) {
                return CategorieVelo::whereNull('cat_id_categorie')
                    ->whereHas('enfants.modeles', function ($q) use ($type) {
                        $q->where('type_velo', $type);
                    })
                    ->with(['enfants' => function ($q) use ($type) {
                        $q->whereHas('modeles', function ($q2) use ($type) {
                            $q2->where('type_velo', $type);
                        })
                        ->with(['modeles' => function ($q3) use ($type) {
                            $q3->where('type_velo', $type);
                        }]);
                    }])
                    ->get();
            };

            // On retourne un tableau avec les deux menus
            return [
                'menuVelo' => $getMenu('musculaire'),
                'menuElec' => $getMenu('electrique'),
            ];
        });

        // On partage les données récupérées du cache (ou de la BDD si cache expiré)
        View::share('menuVelo', $menus['menuVelo']);
        View::share('menuElec', $menus['menuElec']);
    }
}