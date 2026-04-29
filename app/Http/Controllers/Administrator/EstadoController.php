<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;

use App\Models\Estado;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EstadoController extends Controller
{
    public function index(): View
    {
        $estados = Estado::orderBy('nombre')->paginate(15);

        return view('administrator.estados.index', compact('estados'));
    }

    public function create(): View
    {
        $estado = new Estado(['bg' => '#6b7280', 'color' => '#ffffff']);
        return view('administrator.estados.form', ['estado' => $estado]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'icon'   => 'nullable|string|max:50',
            'bg'     => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
            'color'  => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
        ]);

        Estado::create([
            'nombre' => $request->input('nombre'),
            'icon'   => $request->input('icon'),
            'bg'     => $request->input('bg') ?: '#6b7280',
            'color'  => $request->input('color') ?: '#ffffff',
        ]);

        return redirect()->route('estados.index')->with('success', __('Estado creado.'));
    }

    public function edit(Estado $estado): View
    {
        return view('administrator.estados.form', ['estado' => $estado]);
    }

    public function update(Request $request, Estado $estado): RedirectResponse
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'icon'   => 'nullable|string|max:50',
            'bg'     => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
            'color'  => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
        ]);

        $estado->update([
            'nombre' => $request->input('nombre'),
            'icon'   => $request->input('icon'),
            'bg'     => $request->input('bg') ?: '#6b7280',
            'color'  => $request->input('color') ?: '#ffffff',
        ]);

        return redirect()->route('estados.index')->with('success', __('Estado actualizado.'));
    }

    public function destroy(Estado $estado): RedirectResponse
    {
        $estado->delete();
        return redirect()->route('estados.index')->with('success', __('Estado eliminado.'));
    }
}