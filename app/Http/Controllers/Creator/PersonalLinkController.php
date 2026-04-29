<?php

namespace App\Http\Controllers\Creator;

use App\Http\Controllers\Controller;
use App\Http\Requests\PersonalLinkStoreRequest;
use App\Http\Requests\PersonalLinkUpdateRequest;
use App\Models\PersonalLink;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PersonalLinkController extends Controller
{
    private function ensureCreator(Request $request): void
    {
        if ($request->user()->role !== 'creator') {
            abort(403);
        }
    }

    private function ensureOwner(Request $request, PersonalLink $personalLink): void
    {
        if ($personalLink->user_id !== $request->user()->id) {
            abort(403);
        }
    }

    public function index(Request $request): View
    {
        $this->ensureCreator($request);

        $links = $request->user()
            ->personalLinks()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return view('creator.personal-links.index', ['links' => $links]);
    }

    public function create(Request $request): View
    {
        $this->ensureCreator($request);

        return view('creator.personal-links.create');
    }

    public function store(PersonalLinkStoreRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['user_id'] = $request->user()->id;
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);

        PersonalLink::create($data);

        return redirect()->route('personal-links.index')->with('status', __('Enlace guardado.'));
    }

    public function edit(Request $request, PersonalLink $personalLink): View
    {
        $this->ensureCreator($request);
        $this->ensureOwner($request, $personalLink);

        return view('creator.personal-links.edit', ['link' => $personalLink]);
    }

    public function update(PersonalLinkUpdateRequest $request, PersonalLink $personalLink): RedirectResponse
    {
        $this->ensureOwner($request, $personalLink);

        $data = $request->validated();
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);
        $personalLink->update($data);

        return redirect()->route('personal-links.index')->with('status', __('Enlace actualizado.'));
    }

    public function destroy(Request $request, PersonalLink $personalLink): RedirectResponse
    {
        $this->ensureCreator($request);
        $this->ensureOwner($request, $personalLink);

        $personalLink->delete();

        return redirect()->route('personal-links.index')->with('status', __('Enlace eliminado.'));
    }
}
