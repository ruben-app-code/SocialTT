<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserPreferencesInSession
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() && ! session('user_settings_loaded')) {
            $request->user()->syncPreferencesToSession();
        }

        return $next($request);
    }
}
