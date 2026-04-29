<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Duración por defecto del bloqueo (horas)
    |--------------------------------------------------------------------------
    |
    | Si al marcar una cuenta como bloqueada no se indica otra duración,
    | activates_at = blocked_at + estas horas.
    |
    */
    'default_block_duration_hours' => (int) env('SOCIAL_ACCOUNT_BLOCK_HOURS', 24),

];
