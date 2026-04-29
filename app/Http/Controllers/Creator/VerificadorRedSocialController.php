<?php

namespace App\Http\Controllers\Creator;

use App\Http\Controllers\Controller;
use App\Models\SocialNetwork;
use App\Services\SocialProfileVerifier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VerificadorRedSocialController extends Controller
{
    /**
     * GET: comprueba la URL pública del perfil (GET remoto) e intenta leer seguidores del HTML.
     */
    public function __invoke(Request $request, SocialProfileVerifier $verifier): JsonResponse
    {
        $data = $request->validate([
            'social_network_id' => ['required', 'integer', 'exists:social_networks,id'],
            'username' => ['required', 'string', 'max:255'],
        ]);

        $network = SocialNetwork::query()->findOrFail($data['social_network_id']);

        return response()->json($verifier->verify($network->slug, $data['username']));
    }
}
