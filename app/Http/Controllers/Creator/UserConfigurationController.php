<?php

namespace App\Http\Controllers\Creator;

use App\Http\Controllers\Controller;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserConfigurationController extends Controller
{
    public function show(): View
    {
        $user = auth()->user();
        $theme = $user->getPreference('theme', 'system');
        $settingsFromDb = $user->userSettings()->orderBy('key')->get();

        return view('creator.configuration.show', [
            'theme'            => $theme,
            'settingsFromDb'   => $settingsFromDb,
            'sessionTheme'     => session('theme'),
            'sessionSettings'  => session('user_settings', []),
            'sessionLoaded'    => session('user_settings_loaded'),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'theme' => 'required|in:light,dark,system',
        ]);

        $user = auth()->user();
        $theme = $request->input('theme');
        $user->setPreference('theme', $theme);
        $user->syncPreferencesToSession();

        if ($theme === 'system') {
            session()->forget('appearance');
        } else {
            session(['appearance' => $theme]);
        }

        return redirect()->route('configuration.show')->with('success', __('Configuración guardada.'));
    }

    /** Tema claro/oscuro vía GET (barra de navegación). */
    public function setTheme(string $theme): RedirectResponse
    {
        $user = auth()->user();
        $user->setPreference('theme', $theme);
        $user->syncPreferencesToSession();
        session(['appearance' => $theme]);

        return redirect()->back();
    }
}