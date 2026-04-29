<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\TemplateAvatarProfileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SeederProfilePhotoController extends Controller
{
    public function assign(Request $request, TemplateAvatarProfileService $avatars): JsonResponse
    {
        $expected = (string) config('seeders.service_token', '');
        if ($expected === '') {
            abort(403, 'SEEDER_SERVICE_TOKEN no configurado.');
        }

        $token = (string) $request->header('X-Seeder-Token', '');
        if (! hash_equals($expected, $token)) {
            abort(403);
        }

        $validated = $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'loop_index' => 'sometimes|nullable|integer|min:0',
        ]);

        $user = User::query()->findOrFail($validated['user_id']);
        $avatars->applyToUser($user, $validated['loop_index'] ?? null);
        $user->refresh();

        return response()->json([
            'profile_photo_path' => $user->profile_photo_path,
            'avatar_url' => $user->avatar_url,
        ]);
    }
}
