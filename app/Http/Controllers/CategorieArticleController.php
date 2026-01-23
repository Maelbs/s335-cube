<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache; 
use App\Models\CategorieAccessoire;
use App\Models\CategorieVelo;

class CategorieArticleController extends Controller
{
    public function index()
    {
        $data = Cache::remember('home_page_data', 3600, function () {
            
          
            $menuVelo = $this->getCategoriesByBikeType('musculaire');
            $menuElec = $this->getCategoriesByBikeType('electrique');

        
            $categorieAccessoires = CategorieAccessoire::whereNull('cat_id_categorie_accessoire')
                ->with('enfants.enfants') 
                ->get();

            return compact('menuVelo', 'menuElec', 'categorieAccessoires');
        });

      
        return view('accueil', $data);
    }


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