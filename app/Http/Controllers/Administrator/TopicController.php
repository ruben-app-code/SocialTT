<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use App\Http\Requests\TopicStoreRequest;
use App\Http\Requests\TopicUpdateRequest;
use App\Models\Topic;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class TopicController extends Controller
{
    public function index(): View
    {
        $roots = Topic::query()
            ->roots()
            ->with(['children' => fn ($q) => $q->orderBy('name')])
            ->orderBy('name')
            ->get();

        return view('administrator.topics.index', compact('roots'));
    }

    public function create(): View
    {
        return view('administrator.topics.create');
    }

    public function store(TopicStoreRequest $request): RedirectResponse
    {
        Topic::create($request->safe()->only(['name', 'slug', 'parent_id']));

        return redirect()->route('admin.topics.index')->with('status', __('Tema creado.'));
    }

    public function edit(Topic $tema): View
    {
        $tema->load(['parent:id,name']);
        $tema->loadCount('children');

        return view('administrator.topics.edit', ['topic' => $tema]);
    }

    public function update(TopicUpdateRequest $request, Topic $tema): RedirectResponse
    {
        $tema->update($request->safe()->only(['name', 'slug', 'parent_id']));

        return redirect()->route('admin.topics.index')->with('status', __('Tema actualizado.'));
    }

    public function destroy(Topic $tema): RedirectResponse
    {
        if ($tema->children()->exists()) {
            return redirect()->route('admin.topics.index')
                ->with('error', __('No se puede eliminar: tiene subtemas. Elimina o reubica los subtemas primero.'));
        }

        DB::transaction(function () use ($tema) {
            $tema->users()->detach();
            $tema->socialAccounts()->detach();
            $tema->delete();
        });

        return redirect()->route('admin.topics.index')->with('status', __('Tema eliminado.'));
    }

    /**
     * Búsqueda para el selector de tema padre (solo temas raíz si roots_only=1).
     * Misma lógica que en cuentas sociales: mínimo 3 caracteres.
     */
    public function search(Request $request): JsonResponse
    {
        $q = trim((string) $request->query('q', ''));
        if (mb_strlen($q) < 3) {
            return response()->json(['topics' => []]);
        }

        $rootsOnly = $request->boolean('roots_only');
        $excludeId = $request->query('exclude_id');

        $like = '%'.str_replace(['%', '_'], ['\\%', '\\_'], $q).'%';

        $query = Topic::query()
            ->with('parent:id,name')
            ->where('name', 'like', $like)
            ->orderBy('name')
            ->limit(30);

        if ($rootsOnly) {
            $query->whereNull('parent_id');
        }

        if ($excludeId) {
            $query->where('id', '!=', (int) $excludeId);
        }

        $topics = $query->get()->map(fn (Topic $t) => [
            'id' => $t->id,
            'name' => $t->display_name,
        ]);

        return response()->json(['topics' => $topics]);
    }
}
