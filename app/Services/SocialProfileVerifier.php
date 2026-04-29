<?php

namespace App\Services;

use App\Models\SocialNetwork;
use Illuminate\Support\Facades\Http;

/**
 * Comprueba por GET la URL pública del perfil e intenta extraer seguidores y seguidos (u homólogos) del HTML.
 * Las redes cambian el markup o exigen login: resultado orientativo.
 */
class SocialProfileVerifier
{
    private const TIMEOUT_SECONDS = 15;

    /**
     * @return array{
     *     ok: bool,
     *     reachable: bool,
     *     http_status: int|null,
     *     url: string,
     *     followers: int|null,
     *     followers_raw: string|null,
     *     following: int|null,
     *     following_raw: string|null,
     *     login_wall: bool,
     *     message: string|null
     * }
     */
    public function verify(string $slug, string $username): array
    {
        $username = trim($username);
        $url = SocialNetwork::profileUrlForSlug($slug, $username);

        if ($url === '') {
            return $this->result(false, false, null, '', null, null, null, null, false, __('Indica un usuario válido.'));
        }

        try {
            $response = Http::timeout(self::TIMEOUT_SECONDS)
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36',
                    'Accept' => 'text/html,application/xhtml+xml;q=0.9,*/*;q=0.8',
                    'Accept-Language' => 'es-ES,es;q=0.9,en;q=0.8',
                ])
                ->withOptions(['allow_redirects' => true])
                ->get($url);
        } catch (\Throwable $e) {
            return $this->result(false, false, null, $url, null, null, null, null, false, __('No se pudo conectar: :msg', ['msg' => $e->getMessage()]));
        }

        $status = $response->status();
        $body = $response->body();
        $reachable = $status >= 200 && $status < 400;

        $loginWall = $this->detectLoginWall($slug, $body, $status);
        $allow = $reachable && ! $loginWall;

        [$followers, $followersRaw, $following, $followingRaw] = $this->parseMetrics($slug, $body, $allow);

        $message = $this->buildMessage($reachable, $loginWall, $followers, $following, $status);

        return $this->result($reachable, $reachable, $status, $url, $followers, $followersRaw, $following, $followingRaw, $loginWall, $message);
    }

    /**
     * @return array{ok: bool, reachable: bool, http_status: int|null, url: string, followers: int|null, followers_raw: string|null, following: int|null, following_raw: string|null, login_wall: bool, message: string|null}
     */
    private function result(
        bool $ok,
        bool $reachable,
        ?int $httpStatus,
        string $url,
        ?int $followers,
        ?string $followersRaw,
        ?int $following,
        ?string $followingRaw,
        bool $loginWall,
        ?string $message
    ): array {
        return [
            'ok' => $ok,
            'reachable' => $reachable,
            'http_status' => $httpStatus,
            'url' => $url,
            'followers' => $followers,
            'followers_raw' => $followersRaw,
            'following' => $following,
            'following_raw' => $followingRaw,
            'login_wall' => $loginWall,
            'message' => $message,
        ];
    }

    private function buildMessage(bool $reachable, bool $loginWall, ?int $followers, ?int $following, int $status): ?string
    {
        if (! $reachable) {
            return __('La URL respondió con HTTP :status.', ['status' => $status]);
        }
        if ($loginWall) {
            return __('La página pide iniciar sesión; no se pudo leer el perfil público.');
        }
        if ($followers !== null && $following !== null) {
            return __('Perfil accesible; se leyeron seguidores y cuentas seguidas del HTML.');
        }
        if ($followers !== null) {
            return __('Perfil accesible; se leyeron seguidores (no se encontró contador de seguidos).');
        }
        if ($following !== null) {
            return __('Perfil accesible; se leyeron seguidos (no se encontró contador de seguidores).');
        }

        return __('Perfil accesible; no se encontraron contadores en el HTML (normal en muchas redes).');
    }

    private function detectLoginWall(string $slug, string $body, int $status): bool
    {
        if (! ($status >= 200 && $status < 400)) {
            return false;
        }

        $lower = strtolower($body);

        return match ($slug) {
            'instagram' => str_contains($lower, '/accounts/login') || str_contains($lower, 'login_form'),
            'facebook' => $this->detectFacebookLoginWall($lower),
            default => false,
        };
    }

    private function detectFacebookLoginWall(string $lower): bool
    {
        return str_contains($lower, 'login.php')
            || str_contains($lower, 'id="login_form"')
            || str_contains($lower, 'data-testid="royal_login_form"')
            || str_contains($lower, 'data-testid="royal-email"')
            || str_contains($lower, 'must log in to continue')
            || str_contains($lower, 'you must log in')
            || str_contains($lower, 'inicia sesión para continuar')
            || str_contains($lower, 'inicia sesión en facebook')
            || str_contains($lower, '/login/?next=')
            || str_contains($lower, '/login/device-based/')
            || str_contains($lower, 'checkpoint/l/')
            || str_contains($lower, 'checkpoint/?next');
    }

    /**
     * @return array{0: int|null, 1: string|null, 2: int|null, 3: string|null}
     */
    private function parseMetrics(string $slug, string $body, bool $allowParse): array
    {
        if (! $allowParse) {
            return [null, null, null, null];
        }

        return match ($slug) {
            'tiktok' => $this->parseTikTokMetrics($body),
            'youtube' => $this->parseYoutubeMetrics($body),
            'instagram' => $this->parseInstagramMetrics($body),
            'facebook' => $this->parseFacebookMetrics($body),
            'twitter', 'x' => $this->parseTwitterMetrics($body),
            default => [null, null, null, null],
        };
    }

    /**
     * @return array{0: int|null, 1: string|null, 2: int|null, 3: string|null}
     */
    private function parseTikTokMetrics(string $body): array
    {
        [$f, $fR] = $this->extractCountPair($body, [
            ['/"followerCount"\s*:\s*"([^"]+)"/', true],
            ['/"followerCount"\s*:\s*(\d+)/', false],
            ['/"follower_count"\s*:\s*(\d+)/', false],
        ]);
        [$g, $gR] = $this->extractCountPair($body, [
            ['/"followingCount"\s*:\s*"([^"]+)"/', true],
            ['/"followingCount"\s*:\s*(\d+)/', false],
            ['/"following_count"\s*:\s*(\d+)/', false],
        ]);

        return [$f, $fR, $g, $gR];
    }

    /**
     * Suscriptores como “seguidores”; YouTube no expone “siguiendo” en la misma página de forma estable.
     *
     * @return array{0: int|null, 1: string|null, 2: int|null, 3: string|null}
     */
    private function parseYoutubeMetrics(string $body): array
    {
        [$f, $fR] = $this->extractCountPair($body, [
            ['/"subscriberCountText"\s*:\s*\{\s*"simpleText"\s*:\s*"([^"]+)"/', true],
            ['/"subscriberCount"\s*:\s*\{\s*"simpleText"\s*:\s*"([^"]+)"/', true],
        ]);

        return [$f, $fR, null, null];
    }

    /**
     * @return array{0: int|null, 1: string|null, 2: int|null, 3: string|null}
     */
    private function parseInstagramMetrics(string $body): array
    {
        $f = null;
        $fR = null;
        $g = null;
        $gR = null;
        if (preg_match('/"edge_followed_by":\s*\{\s*"count":\s*(\d+)/', $body, $m)) {
            $f = (int) $m[1];
            $fR = $m[1];
        }
        if (preg_match('/"edge_follow":\s*\{\s*"count":\s*(\d+)/', $body, $m)) {
            $g = (int) $m[1];
            $gR = $m[1];
        }

        return [$f, $fR, $g, $gR];
    }

    /**
     * Facebook cambia el HTML con frecuencia; muchas URLs piden login o no incluyen JSON con números.
     * “Seguidores” ≈ me gusta / fans de página o seguidores de perfil si aparecen en el documento.
     *
     * @return array{0: int|null, 1: string|null, 2: int|null, 3: string|null}
     */
    private function parseFacebookMetrics(string $body): array
    {
        [$f, $fR] = $this->extractCountPair($body, [
            ['/"followers_count"\s*:\s*(\d+)/', false],
            ['/"follower_count"\s*:\s*(\d+)/', false],
            ['/"subscription_count"\s*:\s*(\d+)/', false],
            ['/"fan_count"\s*:\s*(\d+)/', false],
            ['/"page_fan_count"\s*:\s*(\d+)/', false],
            ['/"likes_count"\s*:\s*(\d+)/', false],
            ['/"like_count"\s*:\s*(\d+)/', false],
            ['/"global_likes_count"\s*:\s*(\d+)/', false],
            ['/"page_likers_count"\s*:\s*(\d+)/', false],
        ]);

        [$g, $gR] = $this->extractCountPair($body, [
            ['/"following_count"\s*:\s*(\d+)/', false],
            ['/"friends_count"\s*:\s*(\d+)/', false],
            ['/"friend_count"\s*:\s*(\d+)/', false],
        ]);

        if ($f === null) {
            [$f, $fR] = $this->parseFacebookPlaintextCounts($body, true);
        }
        if ($g === null) {
            [$g, $gR] = $this->parseFacebookPlaintextCounts($body, false);
        }

        return [$f, $fR, $g, $gR];
    }

    /**
     * Texto visible en algunas variantes móvil/desktop (muy frágil).
     *
     * @return array{0: int|null, 1: string|null}
     */
    private function parseFacebookPlaintextCounts(string $body, bool $wantFollowers): array
    {
        if ($wantFollowers) {
            $patterns = [
                '/(\d[\d.,]*[kmb]?)\s+people\s+like\s+this/iu',
                '/(\d[\d.,]*[kmb]?)\s+personas\s+les\s+gusta\s+esto/iu',
                '/(\d[\d.,]*[kmb]?)\s+me\s+gusta/iu',
                '/(\d[\d.,]*[kmb]?)\s+followers/iu',
                '/(\d[\d.,]*[kmb]?)\s+seguidores/iu',
            ];
        } else {
            $patterns = [
                '/(\d[\d.,]*[kmb]?)\s+following/iu',
                '/(\d[\d.,]*[kmb]?)\s+siguiendo/iu',
                '/(\d[\d.,]*[kmb]?)\s+friends/iu',
                '/(\d[\d.,]*[kmb]?)\s+amigos/iu',
            ];
        }

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $body, $m)) {
                $n = $this->parseCompactNumber($m[1]);
                if ($n !== null) {
                    return [$n, $m[1]];
                }
            }
        }

        return [null, null];
    }

    /**
     * @return array{0: int|null, 1: string|null, 2: int|null, 3: string|null}
     */
    private function parseTwitterMetrics(string $body): array
    {
        [$f, $fR] = $this->extractCountPair($body, [
            ['/"followers_count"\s*:\s*(\d+)/', false],
            ['/"followersCount"\s*:\s*(\d+)/', false],
        ]);
        [$g, $gR] = $this->extractCountPair($body, [
            ['/"friends_count"\s*:\s*(\d+)/', false],
            ['/"following_count"\s*:\s*(\d+)/', false],
        ]);

        return [$f, $fR, $g, $gR];
    }

    /**
     * @param  list<array{0: string, 1: bool}>  $patterns  [regex, useCompactParse]
     * @return array{0: int|null, 1: string|null}
     */
    private function extractCountPair(string $body, array $patterns): array
    {
        foreach ($patterns as [$pattern, $compact]) {
            if (! preg_match($pattern, $body, $m)) {
                continue;
            }
            $raw = $m[1];
            if ($compact) {
                $n = $this->parseCompactNumber($raw);
                if ($n === null) {
                    continue;
                }
            } else {
                $n = (int) $raw;
            }

            return [$n, $raw];
        }

        return [null, null];
    }

    private function parseCompactNumber(string $raw): ?int
    {
        $raw = trim($raw);
        if ($raw === '') {
            return null;
        }

        $clean = preg_replace('/\s*(subscribers?|seguidores?|followers?|following|likes?|gusta)\s*/iu', '', $raw);
        $clean = trim((string) $clean);

        if (preg_match('/^([\d][\d\.,]*)\s*([kmb])\b/iu', $clean, $m)) {
            $n = (float) str_replace(',', '', $m[1]);
            $mult = match (strtoupper($m[2])) {
                'K' => 1000,
                'M' => 1_000_000,
                'B' => 1_000_000_000,
                default => 1,
            };

            return (int) round($n * $mult);
        }

        $digitsOnly = preg_replace('/\D/', '', $clean);
        if ($digitsOnly !== '' && ctype_digit($digitsOnly)) {
            return (int) $digitsOnly;
        }

        return null;
    }
}
