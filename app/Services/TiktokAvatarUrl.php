<?php

namespace App\Services;

final class TiktokAvatarUrl
{
    /**
     * URL del avatar vía API pública tikwm.com (solo TikTok).
     *
     * @see https://tikwm.com/api/user/info?unique_id=username
     */
    public static function forNetworkSlug(string $networkSlug, string $username): ?string
    {
        if (strtolower($networkSlug) !== 'tiktok') {
            return null;
        }

        return self::fromUsername($username);
    }

    public static function fromUsername(string $username): ?string
    {
        $info = TiktokProfileInfo::fetch($username);

        return $info['avatar_url'] ?? null;
    }
}
