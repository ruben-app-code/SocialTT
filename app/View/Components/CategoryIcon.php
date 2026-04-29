<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class CategoryIcon extends Component
{
    public function __construct(
        public ?string $name = null,
        public string $class = 'size-5'
    ) {}

    public function render(): View|Closure|string
    {
        return view('components.category-icon');
    }
}
