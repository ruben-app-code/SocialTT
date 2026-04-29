<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;

class LangController extends Controller
{
    private const ALLOWED_LOCALES = ['es', 'en'];

    private function path(string $locale): string
    {
        return base_path('lang/' . $locale . '.json');
    }

    private function ensureEnJsonExists(): void
    {
        $path = $this->path('en');
        if (! File::exists($path)) {
            File::put($path, "{\n}\n");
        }
    }

    public function index(): View
    {
        $this->ensureEnJsonExists();
        $locales = [];
        foreach (self::ALLOWED_LOCALES as $locale) {
            $path = $this->path($locale);
            $locales[$locale] = [
                'exists' => File::exists($path),
                'path'   => $path,
            ];
        }

        return view('administrator.lang.index', compact('locales'));
    }

    public function edit(string $locale): View|RedirectResponse
    {
        if (! in_array($locale, self::ALLOWED_LOCALES, true)) {
            abort(404);
        }

        $path = $this->path($locale);
        if ($locale === 'en') {
            $this->ensureEnJsonExists();
        }
        if (! File::exists($path)) {
            return redirect()->route('lang.index')->with('error', __('Archivo de idioma no encontrado.'));
        }

        $content = File::get($path);
        $decoded = json_decode($content, true);
        if (json_last_error() !== JSON_ERROR_NONE || ! is_array($decoded)) {
            $decoded = [];
        }

        // Pares key => value (solo primer nivel; valores escalares)
        $pairs = [];
        foreach ($decoded as $key => $value) {
            $pairs[] = [
                'key'   => $key,
                'value' => is_string($value) ? $value : (string) json_encode($value),
            ];
        }

        return view('administrator.lang.edit', [
            'locale' => $locale,
            'pairs'  => $pairs,
        ]);
    }

    public function update(Request $request, string $locale): RedirectResponse
    {
        if (! in_array($locale, self::ALLOWED_LOCALES, true)) {
            abort(404);
        }

        $keys = $request->input('keys', []);
        $values = $request->input('values', []);

        if (! is_array($keys)) {
            $keys = [];
        }
        if (! is_array($values)) {
            $values = [];
        }

        // Reconstruir objeto clave => valor (mismo orden)
        $translations = [];
        $count = min(count($keys), count($values));
        for ($i = 0; $i < $count; $i++) {
            $key = $keys[$i];
            if ($key !== null && $key !== '') {
                $translations[$key] = $values[$i] ?? '';
            }
        }

        $path = $this->path($locale);
        $encoded = json_encode($translations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        File::put($path, $encoded . "\n");

        return redirect()->route('lang.edit', $locale)->with('success', __('Traducciones guardadas.'));
    }
}