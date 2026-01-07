<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Visit;

class LogVisits
{
    public function handle(Request $request, Closure $next): Response
{
    // --- 1. FILTRE ANTI-POLLUTION ---
    // Si l'utilisateur est sur la page des logs ou une API, on arrête tout de suite.
    if ($request->is('admin-logs') || $request->is('api/*') || $request->is('sanctum/*')) {
        return $next($request);
    }

    /* OPTIONNEL : Si tu veux VRAIMENT uniquement la page d'accueil (et rien d'autre)
       Décommente les 3 lignes ci-dessous :
    */
    // if ($request->path() !== '/') {
    //     return $next($request);
    // }

    // --- 2. LOGIQUE EXISTANTE ---
    $rawCookie = $request->cookie('cube_consent');

    if ($rawCookie) {
        $decodedJson = urldecode($rawCookie);
        $consent = json_decode($decodedJson, true);

        if (($consent['analytics'] ?? false) === true) {
            Visit::create([
                'url' => $request->fullUrl(),
                'ip_address' => $request->ip(), 
                'user_agent' => $request->userAgent(),
                'visited_at' => now(),
            ]);
        }
    }

    return $next($request);
}
}