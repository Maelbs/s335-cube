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
        try {
            $menuVelo = CategorieVelo::whereNull('cat_id_categorie')
                ->whereHas('enfants.modeles', function ($q) {
                    $q->where('type_velo', 'musculaire');
                })
                ->with(['enfants' => function ($q) {
                    $q->whereHas('modeles', function ($q2) {
                        $q2->where('type_velo', 'musculaire');
                    })
                    ->with(['modeles' => function ($q3) {
                        $q3->where('type_velo', 'musculaire');
                    }]);
                }])
                ->get();

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

            View::share('menuVelo', $menuVelo);
            View::share('menuElec', $menuElec);

        } catch (\Exception $e) {
        }
    }
}