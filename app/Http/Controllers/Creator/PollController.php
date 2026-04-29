<?php

namespace App\Http\Controllers\Creator;

use App\Http\Controllers\Controller;

use App\Http\Requests\PollStoreRequest;
use App\Http\Requests\PollUpdateRequest;
use App\Models\Poll;
use App\Models\PollOption;
use App\Models\PollVote;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PollController extends Controller
{
    public function index(Request $request): View
    {
        $polls = $request->user()->polls()->withCount('pollOptions')->latest()->paginate(15);

        return view('creator.poll.index', ['polls' => $polls]);
    }

    public function create(Request $request): View
    {
        return view('creator.poll.create');
    }

    public function store(PollStoreRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $data = [
            'question' => $validated['question'],
            'type' => $validated['type'],
            'is_active' => $validated['is_active'] ?? true,
            'expires_at' => $validated['expires_at'] ?? null,
            'user_id' => $request->user()->id,
        ];

        DB::transaction(function () use ($data, $request) {
            $poll = Poll::create($data);
            $this->createPollOptionsFromRequest($poll, $request);
        });

        return redirect()->route('polls.index')->with('status', __('Encuesta creada.'));
    }

    public function show(Request $request, Poll $poll): View|RedirectResponse
    {
        if ($poll->user_id !== $request->user()->id) {
            abort(403);
        }
        $poll->load('pollOptions');

        return view('creator.poll.show', ['poll' => $poll]);
    }

    public function edit(Request $request, Poll $poll): View|RedirectResponse
    {
        if ($poll->user_id !== $request->user()->id) {
            abort(403);
        }
        $poll->load(['pollOptions' => fn ($q) => $q->orderBy('id')]);

        return view('creator.poll.edit', ['poll' => $poll]);
    }

    public function update(PollUpdateRequest $request, Poll $poll): RedirectResponse
    {
        if ($poll->user_id !== $request->user()->id) {
            abort(403);
        }

        $validated = $request->validated();

        $data = [
            'question' => $validated['question'],
            'type' => $validated['type'],
            'is_active' => $validated['is_active'] ?? true,
            'expires_at' => $validated['expires_at'] ?? null,
        ];

        DB::transaction(function () use ($poll, $data, $request) {
            $poll->update($data);
            $this->syncPollOptionsFromRequest($poll->fresh(), $request);
        });

        return redirect()->route('polls.index')->with('status', __('Encuesta actualizada.'));
    }

    public function destroy(Request $request, Poll $poll): RedirectResponse
    {
        if ($poll->user_id !== $request->user()->id) {
            abort(403);
        }

        $poll->delete();

        return redirect()->route('polls.index')->with('status', __('Encuesta eliminada.'));
    }

    private function createPollOptionsFromRequest(Poll $poll, Request $request): void
    {
        if ($poll->type === 'yes_no') {
            PollOption::create([
                'poll_id' => $poll->id,
                'text' => trim((string) $request->input('yes_text', __('Sí'))),
            ]);
            PollOption::create([
                'poll_id' => $poll->id,
                'text' => trim((string) $request->input('no_text', __('No'))),
            ]);

            return;
        }

        foreach ($request->input('options', []) as $text) {
            $t = trim((string) $text);
            if ($t === '') {
                continue;
            }
            PollOption::create(['poll_id' => $poll->id, 'text' => $t]);
        }
    }

    private function syncPollOptionsFromRequest(Poll $poll, Request $request): void
    {
        if ($poll->type === 'yes_no') {
            $yes = trim((string) $request->input('yes_text', __('Sí')));
            $no = trim((string) $request->input('no_text', __('No')));
            $opts = $poll->pollOptions()->orderBy('id')->get();

            if ($opts->count() >= 2) {
                $opts->first()->update(['text' => $yes]);
                $opts->slice(1, 1)->first()->update(['text' => $no]);
                if ($opts->count() > 2) {
                    $this->deletePollOptionsWithVotes($opts->slice(2)->values());
                }
            } elseif ($opts->count() === 1) {
                $opts->first()->update(['text' => $yes]);
                PollOption::create(['poll_id' => $poll->id, 'text' => $no]);
            } else {
                PollOption::create(['poll_id' => $poll->id, 'text' => $yes]);
                PollOption::create(['poll_id' => $poll->id, 'text' => $no]);
            }

            return;
        }

        $texts = $request->input('option_text', []);
        $ids = $request->input('option_id', []);
        $keptIds = [];

        foreach ($texts as $i => $text) {
            $text = trim((string) $text);
            if ($text === '') {
                continue;
            }
            $id = $ids[$i] ?? null;
            if ($id && $poll->pollOptions()->whereKey($id)->exists()) {
                PollOption::whereKey($id)->update(['text' => $text]);
                $keptIds[] = (int) $id;
            } else {
                $opt = PollOption::create(['poll_id' => $poll->id, 'text' => $text]);
                $keptIds[] = $opt->id;
            }
        }

        $toRemove = $poll->pollOptions()->whereNotIn('id', $keptIds)->pluck('id');
        foreach ($toRemove as $optionId) {
            PollVote::where('option_id', $optionId)->delete();
            PollOption::whereKey($optionId)->delete();
        }
    }

    /**
     * @param  \Illuminate\Support\Collection<int, PollOption>  $options
     */
    private function deletePollOptionsWithVotes($options): void
    {
        foreach ($options as $option) {
            PollVote::where('option_id', $option->id)->delete();
            $option->delete();
        }
    }
}