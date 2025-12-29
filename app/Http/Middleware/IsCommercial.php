<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IsCommercial
{
    public function handle(Request $request, Closure $next)
    {
        // On vérifie si l'utilisateur est connecté ET s'il a le rôle 'commercial'
        if (Auth::check() && Auth::user()->role === 'commercial') {
            return $next($request);
        }

        // Sinon, on le redirige vers l'accueil avec une erreur
        return redirect('/')->with('error', 'Accès réservé aux commerciaux.');
    }
}