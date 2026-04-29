<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;

use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        $categorias = Category::orderBy('nombre')->paginate(15);

        return view('administrator.categories.index', compact('categorias'));
    }

    public function create(): View
    {
        $categoria = new Category(['bg' => '#1E85FF', 'color' => '#ffffff']);
        return view('administrator.categories.form', ['categoria' => $categoria]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'icon'   => 'nullable|string|max:50',
            'bg'     => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
            'color'  => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
        ]);

        Category::create([
            'nombre' => $request->input('nombre'),
            'icon'   => $request->input('icon'),
            'bg'     => $request->input('bg') ?: '#1E85FF',
            'color'  => $request->input('color') ?: '#ffffff',
        ]);

        return redirect()->route('categories.index')->with('success', __('Categoría creada.'));
    }

    public function edit(Category $category): View
    {
        return view('administrator.categories.form', ['categoria' => $category]);
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'icon'   => 'nullable|string|max:50',
            'bg'     => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
            'color'  => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
        ]);

        $category->update([
            'nombre' => $request->input('nombre'),
            'icon'   => $request->input('icon'),
            'bg'     => $request->input('bg') ?: '#1E85FF',
            'color'  => $request->input('color') ?: '#ffffff',
        ]);

        return redirect()->route('categories.index')->with('success', __('Categoría actualizada.'));
    }

    public function destroy(Category $category): RedirectResponse
    {
        $category->delete();
        return redirect()->route('categories.index')->with('success', __('Categoría eliminada.'));
    }
}