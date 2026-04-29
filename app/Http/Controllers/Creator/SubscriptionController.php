<?php

namespace App\Http\Controllers\Creator;

use App\Http\Controllers\Controller;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        return back()->with('status', __('Suscripción registrada.'));
    }
}