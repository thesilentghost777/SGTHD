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
        //
    ];

    /**
     * Determine if the request has a valid CSRF token.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function tokensMatch($request)
    {
        $token = $request->header('X-XSRF-TOKEN');

        if (!$token) {
            $token = $request->cookie('XSRF-TOKEN');
        }

        if (!$token) {
            return false;
        }

        return hash_equals($request->session()->token(), $token);
    }
}
