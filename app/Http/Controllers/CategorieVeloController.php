<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CategorieVelo;

class CategorieVeloController extends Controller
{
    public function index()
    {
        $menuVelo = $this->getCategoriesByBikeType('musculaire');
        $menuElec = $this->getCategoriesByBikeType('electrique');

        return view('accueil', compact('menuVelo', 'menuElec'));
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