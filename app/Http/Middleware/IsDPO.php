<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class IsDPO
{

    public function handle(Request $request, Closure $next)
    {
    
        if (Auth::check() && Auth::user()->role === 'dpo') {
            return $next($request);
        }

    return redirect('/')->with('error', 'Accès non autorisé.');
    }
}