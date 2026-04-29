<?php

namespace App\Http\Controllers\Creator;

use App\Http\Controllers\Controller;

use App\Models\Topic;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Gestiona los temas a los que está afiliado el creador (mis temas).
 */
class UserTopicController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $user->load('topics');
        $rootTopics = Topic::query()
            ->with(['children' => fn ($q) => $q->orderBy('name')])
            ->whereNull('parent_id')
            ->orderBy('name')
            ->get();
        $myTopicIds = $user->topics->pluck('id')->toArray();

        return view('creator.user-topics.index', [
            'rootTopics' => $rootTopics,
            'myTopicIds' => $myTopicIds,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'topic_ids' => ['nullable', 'array'],
            'topic_ids.*' => ['integer', 'exists:topics,id'],
        ]);

        $request->user()->topics()->sync($request->input('topic_ids', []));

        return redirect()->route('user-topics.index')->with('status', __('Temas actualizados.'));
    }
}