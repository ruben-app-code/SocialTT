<?php

namespace App\Http\Controllers\Creator;

use App\Http\Controllers\Controller;

use App\Http\Requests\ScheduleStoreRequest;
use App\Http\Requests\ScheduleUpdateRequest;
use App\Models\Schedule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ScheduleController extends Controller
{
    public function index(Request $request): View
    {
        $schedules = $request->user()->schedules()->orderBy('time')->get();

        return view('creator.schedule.index', [
            'schedules' => $schedules,
        ]);
    }

    public function create(Request $request): View
    {
        return view('creator.schedule.create');
    }

    public function store(ScheduleStoreRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['user_id'] = $request->user()->id;
        Schedule::create($data);

        return redirect()->route('schedules.index')->with('status', __('Horario creado.'));
    }

    public function show(Request $request, Schedule $schedule): View|RedirectResponse
    {
        if ($schedule->user_id !== $request->user()->id) {
            abort(403);
        }

        return view('creator.schedule.show', ['schedule' => $schedule]);
    }

    public function edit(Request $request, Schedule $schedule): View|RedirectResponse
    {
        if ($schedule->user_id !== $request->user()->id) {
            abort(403);
        }

        return view('creator.schedule.edit', ['schedule' => $schedule]);
    }

    public function update(ScheduleUpdateRequest $request, Schedule $schedule): RedirectResponse
    {
        if ($schedule->user_id !== $request->user()->id) {
            abort(403);
        }

        $schedule->update($request->validated());

        return redirect()->route('schedules.index')->with('status', __('Horario actualizado.'));
    }

    public function destroy(Request $request, Schedule $schedule): RedirectResponse
    {
        if ($schedule->user_id !== $request->user()->id) {
            abort(403);
        }

        $schedule->delete();

        return redirect()->route('schedules.index')->with('status', __('Horario eliminado.'));
    }
}