<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use App\Http\ViewComposers\HeaderComposer;
use App\Models\CategorieAccessoire;
use App\Models\Panier;
use App\Models\LignePanier;
use App\Models\VarianteVelo;
use App\Models\Accessoire;
use App\Models\MagasinPartenaire;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot()
    {
        View::composer('layouts.header', HeaderComposer::class);

        View::composer('*', function ($view) {
        
            $magasinHeader = null;
            if (Auth::check() && Auth::user()->id_magasin) {
                $magasinHeader = MagasinPartenaire::with('adresses')->find(Auth::user()->id_magasin);
            } elseif (Session::has('id_magasin_choisi')) {
                $magasinHeader = MagasinPartenaire::with('adresses')->find(Session::get('id_magasin_choisi'));
            }
            $view->with('magasinHeader', $magasinHeader);

            $tousLesMagasins = \Illuminate\Support\Facades\Cache::remember('list_magasins_global', 60, function () {
                return MagasinPartenaire::with('adresses')->get();
            });
    
            $view->with('tousLesMagasins', $tousLesMagasins);
        });
    }
}