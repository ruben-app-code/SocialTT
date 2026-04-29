<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Token para el endpoint API de seed (foto de perfil desde plantilla).
    |--------------------------------------------------------------------------
    |
    | Si está vacío, las peticiones a /api/seed/assign-profile-photo devuelven 403.
    | Los seeders usan TemplateAvatarProfileService directamente; el token es opcional
    | para llamadas HTTP externas (scripts, Postman, otro servicio).
    |
    */

    'service_token' => env('SEEDER_SERVICE_TOKEN'),

];
