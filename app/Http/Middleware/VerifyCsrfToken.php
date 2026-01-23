<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * Les routes qui doivent être exemptées de la vérification du jeton CSRF.
     *
     * @var array
     */
    protected $except = [
        'chat/ask', 
        'auth/google/callback',
    ];
}