<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Tras TrustProxies, si la petición es HTTPS y SESSION_SECURE_COOKIE no está fijado en .env,
 * fuerza cookie de sesión Secure. Sin esto, en algunos entornos (vhost .test + proxy) la cookie
 * no se reenvía en el POST siguiente y Laravel genera 419 (token CSRF ≠ sesión).
 */
class ConfigureSessionForHttps
{
    public function handle(Request $request, Closure $next): Response
    {
        $isHttps = $request->secure()
            || $request->headers->get('X-Forwarded-Proto') === 'https'
            || $request->server->get('HTTPS') === 'on'
            || $request->server->get('SERVER_PORT') === '443';

        // Solo si no hay valor explícito en config (null = "auto"). false/true explícito en .env se respeta.
        if (config('session.secure') === null && $isHttps) {
            config(['session.secure' => true]);
        }

        return $next($request);
    }
}
