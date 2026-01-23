<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CacheLongueDuree
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if (method_exists($response, 'header') && $response->getStatusCode() === 200) {
            $response->header('Cache-Control', 'public, max-age=31536000, immutable');
            $response->header('Expires', gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT');

            $response->headers->remove('Pragma');
        }

        return $response;
    }
}