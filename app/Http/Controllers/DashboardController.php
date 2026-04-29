<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $dashboardUser = $request->user()->loadMissing(['socialAccounts.socialNetwork']);

        return view('creator.dashboard', [
            'dashboardUser' => $dashboardUser,
        ]);
    }
}
