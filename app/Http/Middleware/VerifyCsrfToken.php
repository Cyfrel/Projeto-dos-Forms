<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        'create-forms',
        'store-forms',
        'show-forms',
        'list-forms',
        'enviar-post-forms',
        'create-user',
        'show-user',
        'create-respostas',
        'create-perguntas',
        'list-respostas',
    ];
}
