<?php

namespace Database\Seeders\Concerns;

use Database\Seeders\SeedProgress;

trait SeedsWithProgress
{
    /**
     * Avance en consola: números en la misma línea (empieza con dos espacios).
     * Al acabar el ciclo de ejecución (fin del comando), salto CRLF y dos espacios.
     */
    protected function seedDot(): void
    {
        SeedProgress::dot();
    }
}
