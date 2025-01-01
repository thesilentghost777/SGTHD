<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    protected $middlewareAliases = [
        // ... autres middlewares
        'role' => \App\Http\Middleware\CheckRole::class,
    ];
}