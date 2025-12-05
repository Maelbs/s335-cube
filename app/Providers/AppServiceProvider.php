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

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot()
    {
        View::composer('layouts.header', HeaderComposer::class);
    }
}