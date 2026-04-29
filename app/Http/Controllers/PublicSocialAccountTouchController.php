<?php

namespace App\Http\Controllers;

use App\Models\FollowerSnapshot;
use App\Models\SocialAccount;
use App\Services\SocialProfileVerifier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Llamada silenciosa desde el perfil público al hacer clic en una red no verificada.
 * URL firmada + CSRF (misma pestaña) limitan abuso.
 */
class PublicSocialAccountTouchController extends Controller
{
    public function store(Request $request, SocialAccount $socialAccount, SocialProfileVerifier $verifier): JsonResponse
    {
        if (! $socialAccount->isUsableOnPublicProfile() || ! $socialAccount->url) {
            return response()->json(['ok' => false], 404);
        }

        if ($socialAccount->is_verified) {
            return response()->json(['ok' => true, 'verified' => true, 'skipped' => true]);
        }

        $network = $socialAccount->socialNetwork;
        if (! $network) {
            return response()->json(['ok' => false], 404);
        }

        $result = $verifier->verify($network->slug, $socialAccount->username);

        $socialAccount->last_checked_at = now();

        if ($result['followers'] !== null || $result['following'] !== null) {
            FollowerSnapshot::query()->create([
                'user_id' => $socialAccount->user_id,
                'social_account_id' => $socialAccount->id,
                'followers_count' => $result['followers'],
                'following_count' => $result['following'],
                'source' => 'auto_prompt',
                'recorded_at' => now(),
            ]);
        }

        if ($result['reachable'] && ! $result['login_wall']) {
            $socialAccount->is_verified = true;
        }

        $socialAccount->save();

        return response()->json([
            'ok' => true,
            'verified' => (bool) $socialAccount->is_verified,
            'reachable' => $result['reachable'],
            'followers' => $result['followers'],
            'following' => $result['following'],
        ]);
    }
}
