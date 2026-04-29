<?php

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Illuminate\Support\Str;

class FeatherIcon extends Component
{
    public string $name;

    public string $svg;

    public function __construct(string $name)
    {
        $this->name = $name;
        $path = public_path('Feather-icons/' . Str::kebab($name) . '.svg');
        $svg = is_file($path) ? file_get_contents($path) : '';

        if ($svg !== '') {
            $svg = preg_replace('/\s(width|height)="[^"]*"/', ' width="100%" height="100%"', $svg);
            $this->svg = $svg;
        } else {
            $this->svg = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/></svg>';
        }
    }

    public function render(): View
    {
        return view('components.feather-icon');
    }
}
