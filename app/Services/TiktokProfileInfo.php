<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Throwable;

/** Datos de perfil público TikTok vía tikwm.com (una sola petición HTTP). */
final class TiktokProfileInfo
{
    /**
     * @return array{avatar_url: string|null, nickname: string|null, follower_count: int|null, following_count: int|null, heart_count: int|null}|null
     */
    public static function fetch(?string $username): ?array
    {
        $handle = $username === null ? '' : ltrim(trim($username), '@');
        if ($handle === '') {
            return null;
        }

        try {
            $response = Http::timeout(15)
                ->acceptJson()
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (compatible; SocialTikTok/1.0)',
                ])
                ->get('https://tikwm.com/api/user/info', [
                    'unique_id' => $handle,
                ]);

            if (! $response->ok()) {
                return null;
            }

            if ((int) $response->json('code') !== 0) {
                return null;
            }

            $avatarUrl = null;
            foreach (['data.user.avatarLarger', 'data.user.avatarMedium', 'data.user.avatarThumb'] as $key) {
                $avatar = $response->json($key);
                if (is_string($avatar) && $avatar !== '' && filter_var($avatar, FILTER_VALIDATE_URL) !== false) {
                    $avatarUrl = mb_substr($avatar, 0, 2048);
                    break;
                }
            }

            $nickname = $response->json('data.user.nickname');
            $nickname = is_string($nickname) && $nickname !== ''
                ? mb_substr($nickname, 0, 255)
                : null;

            $stats = $response->json('data.stats');
            $followerCount = null;
            $followingCount = null;
            $heartCount = null;
            if (is_array($stats)) {
                if (array_key_exists('followerCount', $stats)) {
                    $followerCount = (int) $stats['followerCount'];
                }
                if (array_key_exists('followingCount', $stats)) {
                    $followingCount = (int) $stats['followingCount'];
                }
                if (array_key_exists('heartCount', $stats)) {
                    $heartCount = (int) $stats['heartCount'];
                }
            }

            return [
                'avatar_url' => $avatarUrl,
                'nickname' => $nickname,
                'follower_count' => $followerCount,
                'following_count' => $followingCount,
                'heart_count' => $heartCount,
            ];
        } catch (Throwable) {
            return null;
        }
    }
}
