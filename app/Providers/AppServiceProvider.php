<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
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
        View::composer('*', function ($view) {
            
            $accessoiresMenu = CategorieAccessoire::whereNull('cat_id_categorie_accessoire')
                ->with('enfants.enfants')
                ->get();
                
            $view->with('categorieAccessoires', $accessoiresMenu);
        });
    }
}
