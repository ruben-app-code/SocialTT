<?php

namespace App\Providers;

use App\Listeners\LoadUserPreferencesAfterLogin;
use App\Livewire\SubdirectoryAwareHandleRequests;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Livewire\Mechanisms\HandleRequests\HandleRequests;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(HandleRequests::class, SubdirectoryAwareHandleRequests::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Event::listen(Login::class, LoadUserPreferencesAfterLogin::class);

        $root = rtrim((string) config('app.url'), '/');
        if ($root === '') {
            return;
        }

        // En consola (migraciones, colas) usar siempre APP_URL para generar enlaces.
        if ($this->app->runningInConsole()) {
            URL::forceRootUrl($root);

            return;
        }

        // En HTTP: solo forzar la raíz si el host de la petición coincide con APP_URL.
        // Si APP_URL apunta a otro host (p. ej. localhost) y entras por https://digitalizacion.test,
        // forzar aquí rompe redirecciones post-login y la cookie de sesión parece "no guardarse".
        if ($this->app->bound('request')) {
            $appHost = parse_url($root, PHP_URL_HOST);
            $requestHost = request()->getHost();
            if ($appHost && $requestHost && strcasecmp((string) $appHost, (string) $requestHost) === 0) {
                URL::forceRootUrl($root);
            }
        }
    }
}
