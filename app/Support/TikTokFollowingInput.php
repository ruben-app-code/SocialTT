<?php

namespace App\Support;

/**
 * Interpreta pegados de perfil TikTok (URL o @usuario) y devuelve el unique_id/handle para la API y la BD.
 */
final class TikTokFollowingInput
{
    /**
     * @see https://www.tiktok.com/@username
     */
    public static function normalizeToHandle(string $raw): ?string
    {
        $raw = trim($raw);
        if ($raw === '') {
            return null;
        }

        $candidate = $raw;

        if (! preg_match('#^https?://#i', $candidate) && str_contains(strtolower($candidate), 'tiktok.com')) {
            $candidate = 'https://'.ltrim($candidate, '/');
        }

        if (preg_match('#^https?://#i', $candidate)) {
            $parts = parse_url($candidate);
            if (! is_array($parts) || empty($parts['host'])) {
                return null;
            }

            $host = strtolower($parts['host']);
            if (! str_ends_with($host, 'tiktok.com')) {
                return null;
            }

            $path = $parts['path'] ?? '';
            if ($path !== '' && preg_match('#^/@([a-zA-Z0-9._]+)(?:/.*)?$#', $path, $m)) {
                return self::clipHandle($m[1]);
            }

            if (preg_match('#tiktok\.com/@([a-zA-Z0-9._]+)#i', $candidate, $m)) {
                return self::clipHandle($m[1]);
            }

            return null;
        }

        $plain = ltrim($candidate, '@');
        if ($plain !== '' && preg_match('/^[a-zA-Z0-9._]+$/', $plain)) {
            return self::clipHandle($plain);
        }

        return null;
    }

    /**
     * Valor del campo del formulario: siempre "@usuario" en minúsculas (el enlace completo va aparte).
     *
     * @param  string|null  $oldSubmitted  valor de old('username') tras validación
     * @param  string|null  $storedHandle  username guardado en BD (sin @)
     */
    public static function formFieldValue(?string $oldSubmitted, ?string $storedHandle): string
    {
        if (is_string($oldSubmitted) && $oldSubmitted !== '') {
            $h = self::normalizeToHandle($oldSubmitted);
            if ($h !== null) {
                return '@'.mb_strtolower($h);
            }

            return $oldSubmitted;
        }
        if (is_string($storedHandle) && $storedHandle !== '') {
            return '@'.mb_strtolower(ltrim($storedHandle, '@'));
        }

        return '';
    }

    private static function clipHandle(string $handle): string
    {
        return mb_substr($handle, 0, 255);
    }
}
