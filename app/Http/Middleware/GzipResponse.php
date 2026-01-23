<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class GzipResponse
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        $content = $response->content();

        if (in_array('gzip', $request->getEncodings()) && function_exists('gzencode')) {
            $response->setContent(gzencode($content, 9));
            $response->headers->add([
                'Content-Encoding' => 'gzip',
                'Content-Length'   => strlen($response->content()),
            ]);
        }
        return $response;
    }
}
