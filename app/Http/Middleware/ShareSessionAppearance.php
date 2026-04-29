<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

/**
 * Tema claro/oscuro: prioridad session('appearance') light|dark; si no hay, usuario autenticado con session('theme').
 */
class ShareSessionAppearance
{
    public function handle(Request $request, Closure $next): Response
    {
        $appearance = session('appearance');

        if ($appearance === 'dark') {
            View::share('appearanceIsDark', true);
            View::share('appearanceUseSystemScript', false);
        } elseif ($appearance === 'light') {
            View::share('appearanceIsDark', false);
            View::share('appearanceUseSystemScript', false);
        } elseif ($request->user()) {
            $theme = session('theme', 'system');
            if ($theme === 'dark') {
                View::share('appearanceIsDark', true);
                View::share('appearanceUseSystemScript', false);
            } elseif ($theme === 'light') {
                View::share('appearanceIsDark', false);
                View::share('appearanceUseSystemScript', false);
            } else {
                View::share('appearanceIsDark', false);
                View::share('appearanceUseSystemScript', true);
            }
        } else {
            View::share('appearanceIsDark', false);
            View::share('appearanceUseSystemScript', false);
        }

        return $next($request);
    }
}
