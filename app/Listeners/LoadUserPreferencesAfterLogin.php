<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;

class LoadUserPreferencesAfterLogin
{
    public function handle(Login $event): void
    {
        $event->user->syncPreferencesToSession();
    }
}
