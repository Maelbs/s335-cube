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
  
    if ($request->is('admin-logs') || $request->is('api/*') || $request->is('sanctum/*')) {
        return $next($request);
    }

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