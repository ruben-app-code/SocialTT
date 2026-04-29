<?php

namespace App\Livewire;

use Livewire\Mechanisms\HandleRequests\HandleRequests;

/**
 * Livewire usa route(..., false) para data-update-uri, lo que genera "/livewire/update"
 * y el navegador lo resuelve al raíz del host (p. ej. http://localhost:99/livewire/update),
 * ignorando la subcarpeta del proyecto. Una URL absoluta respeta APP_URL y URL::forceRootUrl.
 */
class SubdirectoryAwareHandleRequests extends HandleRequests
{
    public function getUpdateUri(): string
    {
        $route = $this->updateRoute ?? $this->findUpdateRoute();

        return route($route->getName(), [], true);
    }
}
