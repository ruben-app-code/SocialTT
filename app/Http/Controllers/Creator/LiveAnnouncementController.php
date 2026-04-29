<?php

namespace App\Http\Controllers\Creator;

use App\Http\Controllers\Controller;

use App\Http\Requests\LiveAnnouncementStoreRequest;
use App\Http\Requests\LiveAnnouncementUpdateRequest;
use App\Models\LiveAnnouncement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LiveAnnouncementController extends Controller
{
    public function index(Request $request): View
    {
        $liveAnnouncements = $request->user()->liveAnnouncements()->orderBy('scheduled_at')->paginate(15);

        return view('creator.live-announcement.index', [
            'liveAnnouncements' => $liveAnnouncements,
        ]);
    }

    public function create(Request $request): View
    {
        return view('creator.live-announcement.create');
    }

    public function store(LiveAnnouncementStoreRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['user_id'] = $request->user()->id;
        LiveAnnouncement::create($data);

        return redirect()->route('live-announcements.index')->with('status', __('Anuncio de directo creado.'));
    }

    public function show(Request $request, LiveAnnouncement $liveAnnouncement): View|RedirectResponse
    {
        if ($liveAnnouncement->user_id !== $request->user()->id) {
            abort(403);
        }

        return view('creator.live-announcement.show', ['liveAnnouncement' => $liveAnnouncement]);
    }

    public function edit(Request $request, LiveAnnouncement $liveAnnouncement): View|RedirectResponse
    {
        if ($liveAnnouncement->user_id !== $request->user()->id) {
            abort(403);
        }

        return view('creator.live-announcement.edit', ['liveAnnouncement' => $liveAnnouncement]);
    }

    public function update(LiveAnnouncementUpdateRequest $request, LiveAnnouncement $liveAnnouncement): RedirectResponse
    {
        if ($liveAnnouncement->user_id !== $request->user()->id) {
            abort(403);
        }

        $liveAnnouncement->update($request->validated());

        return redirect()->route('live-announcements.index')->with('status', __('Anuncio actualizado.'));
    }

    public function destroy(Request $request, LiveAnnouncement $liveAnnouncement): RedirectResponse
    {
        if ($liveAnnouncement->user_id !== $request->user()->id) {
            abort(403);
        }

        $liveAnnouncement->delete();

        return redirect()->route('live-announcements.index')->with('status', __('Anuncio eliminado.'));
    }
}