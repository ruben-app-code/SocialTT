<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Evita desajuste de HTTPS/detector de URL detrás de proxy local (Herd, Vite, etc.) y problemas de sesión/CSRF.
        $middleware->trustProxies(at: '*');

        // Antes de StartSession: cookie Secure en HTTPS si no está definida en .env (evita 419 en login).
        $middleware->prependToGroup('web', \App\Http\Middleware\ConfigureSessionForHttps::class);

        $middleware->web(append: [
            \App\Http\Middleware\EnsureUserPreferencesInSession::class,
            \App\Http\Middleware\ShareSessionAppearance::class,
            \App\Http\Middleware\EnsureUserIsActive::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
