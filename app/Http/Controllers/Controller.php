<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache; 
use App\Models\CategorieVelo;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function __construct()
    {
        $menus = Cache::remember('menus_header', 60 * 60, function () {

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

            return [
                'menuVelo' => $getMenu('musculaire'),
                'menuElec' => $getMenu('electrique'),
            ];
        });

        View::share('menuVelo', $menus['menuVelo']);
        View::share('menuElec', $menus['menuElec']);
    }
}