<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureClientAddress
{
    public function handle($request, Closure $next)
    {
        $client = auth()->user();

        if ($client && (empty($client->tel) || empty($client->id_adresse_facturation))) {
            // On autorise l'accès seulement aux routes de complétion et de logout
            if (!$request->routeIs('client.complete_profile') && 
                !$request->routeIs('client.save_profile') && 
                !$request->routeIs('logout')) {
                
                return redirect()->route('client.complete_profile');
            }
        }

        return $next($request);
    }
}
