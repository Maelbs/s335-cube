<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache; // <--- Indispensable pour la performance
use App\Models\CategorieAccessoire;
use App\Models\CategorieVelo;

class CategorieArticleController extends Controller
{
    public function index()
    {
        // On met en cache TOUTES les données de la page d'accueil pour 1 heure (3600s).
        // La clé de cache est 'home_page_data'.
        $data = Cache::remember('home_page_data', 3600, function () {
            
            // 1. Récupération des Menus Vélos (Optimisé)
            $menuVelo = $this->getCategoriesByBikeType('musculaire');
            $menuElec = $this->getCategoriesByBikeType('electrique');

            // 2. Récupération des Accessoires (Optimisé)
            $categorieAccessoires = CategorieAccessoire::whereNull('cat_id_categorie_accessoire')
                ->with('enfants.enfants') // Eager Loading pour éviter les requêtes en boucle
                ->get();

            // On retourne le tableau complet prêt à être envoyé à la vue
            return compact('menuVelo', 'menuElec', 'categorieAccessoires');
        });

        // On passe les données à la vue
        return view('accueil', $data);
    }

    // Cette fonction est appelée à l'intérieur du Cache, donc elle ne s'exécutera pas à chaque visite
    private function getCategoriesByBikeType($type)
    {
        return CategorieVelo::whereNull('cat_id_categorie')
            ->whereHas('enfants.modeles', function ($query) use ($type) {
                $query->where('type_velo', $type);
            })
            ->with(['enfants' => function ($query) use ($type) {
                $query->whereHas('modeles', function ($q) use ($type) {
                    $q->where('type_velo', $type);
                })
                ->with(['modeles' => function ($q) use ($type) {
                    $q->where('type_velo', $type);
                }]);
            }])
            ->get();
    }
}