<?php

namespace Database\Seeders;

/**
 * Salida de progreso en seeders: números en una sola línea; al terminar el proceso, CRLF + dos espacios.
 */
final class SeedProgress
{
    private static int $count = 0;

    private static bool $shutdownRegistered = false;

    public static function dot(): void
    {
        if (! self::$shutdownRegistered) {
            self::$shutdownRegistered = true;
            register_shutdown_function([self::class, 'flushLine']);
        }

        if (self::$count === 0) {
            echo '  ';
        } else {
            echo ' ';
        }

        echo ++self::$count;

        self::flushOutput();
    }

    public static function flushLine(): void
    {
        if (self::$count > 0) {
            echo "\r\n  ";
            self::flushOutput();
            self::$count = 0;
        }
        self::$shutdownRegistered = false;
    }

    private static function flushOutput(): void
    {
        if (\function_exists('ob_get_level') && ob_get_level() > 0) {
            @ob_flush();
        }
        flush();
    }
}
