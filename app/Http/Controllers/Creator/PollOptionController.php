<?php

namespace App\Http\Controllers\Creator;

use App\Http\Controllers\Controller;

use App\Http\Requests\PollOptionStoreRequest;
use App\Http\Requests\PollOptionUpdateRequest;
use App\Models\PollOption;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PollOptionController extends Controller
{
    public function index(Request $request): View
    {
        $pollOptions = PollOption::all();

        return view('creator.poll-option.index', [
            'pollOptions' => $pollOptions,
        ]);
    }

    public function create(Request $request): View
    {
        return view('creator.poll-option.create');
    }

    public function store(PollOptionStoreRequest $request): RedirectResponse
    {
        $pollOption = PollOption::create($request->validated());

        $request->session()->flash('pollOption.id', $pollOption->id);

        return redirect()->route('pollOptions.index');
    }

    public function show(Request $request, PollOption $pollOption): View
    {
        return view('creator.poll-option.show', [
            'pollOption' => $pollOption,
        ]);
    }

    public function edit(Request $request, PollOption $pollOption): View
    {
        return view('creator.poll-option.edit', [
            'pollOption' => $pollOption,
        ]);
    }

    public function update(PollOptionUpdateRequest $request, PollOption $pollOption): RedirectResponse
    {
        $pollOption->update($request->validated());

        $request->session()->flash('pollOption.id', $pollOption->id);

        return redirect()->route('pollOptions.index');
    }

    public function destroy(Request $request, PollOption $pollOption): RedirectResponse
    {
        $pollOption->delete();

        return redirect()->route('pollOptions.index');
    }
}