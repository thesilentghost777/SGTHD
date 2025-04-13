<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CheckImpersonation
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (Session::has('impersonating') && Session::get('impersonating')) {
            // Partager la variable impersonating avec toutes les vues
            view()->share('impersonating', true);
        }

        return $next($request);
    }
}
