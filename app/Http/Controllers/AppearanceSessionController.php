<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AppearanceSessionController extends Controller
{
    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'appearance' => ['required', 'string', 'in:light,dark'],
        ]);

        session(['appearance' => $validated['appearance']]);

        return response()->json([
            'ok' => true,
            'appearance' => $validated['appearance'],
        ]);
    }
}
