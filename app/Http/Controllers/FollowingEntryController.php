<?php

namespace App\Http\Controllers;

use App\Models\FollowingEntry;
use App\Models\SocialNetwork;
use App\Models\User;
use App\Services\TiktokProfileInfo;
use App\Support\TikTokFollowingInput;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FollowingEntryController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $entries = $user->followingEntries()
            ->with(['platformUser', 'socialNetwork', 'latestFollowerSnapshot'])
            ->get();

        $q = trim((string) $request->query('q', ''));
        $searchResults = collect();
        if (mb_strlen($q) >= 2) {
            $searchResults = $this->searchPlatformCreators($user, $q, $entries);
        }

        return view('creator.following.index', [
            'entries' => $entries,
            'searchResults' => $searchResults,
            'searchQuery' => $q,
        ]);
    }

    public function create(Request $request): View|RedirectResponse
    {
        $tiktok = $this->tiktokNetwork();
        if ($tiktok === null) {
            return redirect()
                ->route('following.index')
                ->with('error', __('No hay red TikTok configurada. Contacta al administrador.'));
        }

        return view('creator.following.create', [
            'tiktokNetwork' => $tiktok,
        ]);
    }

    public function storeExternal(Request $request): RedirectResponse
    {
        $tiktok = $this->tiktokNetwork();
        if ($tiktok === null) {
            return redirect()
                ->route('following.index')
                ->with('error', __('No hay red TikTok configurada.'));
        }

        $validated = $request->validate([
            'username' => ['required', 'string', 'max:2048'],
            'label' => ['nullable', 'string', 'max:255'],
            'note' => ['nullable', 'string', 'max:2000'],
        ]);

        $username = TikTokFollowingInput::normalizeToHandle($validated['username']);
        if ($username === null) {
            return redirect()
                ->route('following.create')
                ->withInput()
                ->withErrors(['username' => __('No se reconoce el texto. Pega el enlace del perfil (p. ej. https://www.tiktok.com/@usuario) o escribe el @usuario.')]);
        }

        $username = mb_strtolower($username);
        $url = SocialNetwork::profileUrlForSlug($tiktok->slug, $username);

        $dup = FollowingEntry::query()
            ->where('user_id', $request->user()->id)
            ->where('entry_type', FollowingEntry::TYPE_EXTERNAL)
            ->where('social_network_id', $tiktok->id)
            ->whereRaw('LOWER(username) = ?', [$username])
            ->exists();

        if ($dup) {
            return redirect()
                ->route('following.create')
                ->withInput()
                ->withErrors(['username' => __('Ya tienes esta cuenta en tu lista.')]);
        }

        $profile = TiktokProfileInfo::fetch($username);
        if ($profile === null) {
            return redirect()
                ->route('following.create')
                ->withInput()
                ->withErrors(['username' => __('No se encontró la cuenta en TikTok o el servicio no respondió. Comprueba que el perfil exista y vuelve a intentarlo.')]);
        }

        $useCustomAvatar = $request->boolean('use_custom_avatar');

        $entry = FollowingEntry::create([
            'user_id' => $request->user()->id,
            'entry_type' => FollowingEntry::TYPE_EXTERNAL,
            'platform_user_id' => null,
            'social_network_id' => $tiktok->id,
            'username' => $username,
            'remote_display_name' => $profile['nickname'] ?? null,
            'url' => $url,
            'avatar_url' => $useCustomAvatar ? null : ($profile['avatar_url'] ?? null),
            'use_custom_avatar' => $useCustomAvatar,
            'label' => $validated['label'] ?? null,
            'note' => $validated['note'] ?? null,
        ]);

        $this->persistFollowerSnapshotFromTiktokProfile($entry, $profile);

        return redirect()
            ->route('following.index')
            ->with('success', __('Cuenta TikTok añadida.'));
    }

    public function storePlatform(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'creator_id' => ['required', 'integer', 'exists:users,id'],
            'label' => ['nullable', 'string', 'max:255'],
            'note' => ['nullable', 'string', 'max:2000'],
            'use_custom_avatar' => ['sometimes', 'boolean'],
        ]);

        $creator = User::query()->findOrFail($validated['creator_id']);

        if ((int) $creator->id === (int) $request->user()->id) {
            $q = $request->input('q', $request->query('q'));

            return redirect()
                ->route('following.index', array_filter(['q' => $q ? (string) $q : null]))
                ->withErrors(['creator' => __('No puedes añadirte a ti mismo.')]);
        }

        if ($creator->role !== 'creator') {
            $q = $request->input('q', $request->query('q'));

            return redirect()
                ->route('following.index', array_filter(['q' => $q ? (string) $q : null]))
                ->withErrors(['creator' => __('Solo puedes añadir perfiles de creador.')]);
        }

        $exists = FollowingEntry::query()
            ->where('user_id', $request->user()->id)
            ->where('entry_type', FollowingEntry::TYPE_PLATFORM)
            ->where('platform_user_id', $creator->id)
            ->exists();

        if ($exists) {
            $q = $request->input('q', $request->query('q'));

            return redirect()
                ->route('following.index', array_filter(['q' => $q ? (string) $q : null]))
                ->with('error', __('Ese creador ya está en tu lista.'));
        }

        $useCustomAvatar = $request->boolean('use_custom_avatar');
        [$snap, $profile] = $this->platformFollowingSnapshotWithProfile($creator, $useCustomAvatar, null);

        $entry = FollowingEntry::create([
            'user_id' => $request->user()->id,
            'entry_type' => FollowingEntry::TYPE_PLATFORM,
            'platform_user_id' => $creator->id,
            'social_network_id' => null,
            'username' => null,
            'remote_display_name' => $snap['remote_display_name'] ?? null,
            'url' => $snap['url'],
            'avatar_url' => $snap['avatar_url'],
            'use_custom_avatar' => $useCustomAvatar,
            'label' => $validated['label'] ?? null,
            'note' => $validated['note'] ?? null,
        ]);

        $this->persistFollowerSnapshotFromTiktokProfile($entry, $profile);

        $q = $request->input('q', $request->query('q'));

        return redirect()
            ->route('following.index', array_filter(['q' => $q ? (string) $q : null]))
            ->with('success', __('Creador añadido a tu lista.'));
    }

    public function edit(Request $request, FollowingEntry $followingEntry): View|RedirectResponse
    {
        abort_unless($followingEntry->user_id === $request->user()->id, 403);

        $followingEntry->load(['platformUser', 'socialNetwork']);

        $tiktok = $this->tiktokNetwork();
        if ($tiktok === null) {
            return redirect()
                ->route('following.index')
                ->with('error', __('No hay red TikTok configurada.'));
        }

        if ($followingEntry->isExternal()) {
            $this->syncExternalFollowingFromTiktok($followingEntry, $tiktok);

            return view('creator.following.edit', [
                'entry' => $followingEntry->fresh(['platformUser', 'socialNetwork']),
                'tiktokNetwork' => $tiktok,
            ]);
        }

        $this->syncPlatformFollowingEntryFromCreator($followingEntry);

        return view('creator.following.edit', [
            'entry' => $followingEntry->fresh(['platformUser', 'socialNetwork']),
            'tiktokNetwork' => $tiktok,
        ]);
    }

    public function update(Request $request, FollowingEntry $followingEntry): RedirectResponse
    {
        abort_unless($followingEntry->user_id === $request->user()->id, 403);

        $tiktok = $this->tiktokNetwork();
        if ($tiktok === null) {
            return redirect()
                ->route('following.index')
                ->with('error', __('No hay red TikTok configurada.'));
        }

        if ($followingEntry->isExternal()) {
            $validated = $request->validate([
                'username' => ['required', 'string', 'max:2048'],
                'label' => ['nullable', 'string', 'max:255'],
                'note' => ['nullable', 'string', 'max:2000'],
                'use_custom_avatar' => ['sometimes', 'boolean'],
            ]);

            $username = TikTokFollowingInput::normalizeToHandle($validated['username']);
            if ($username === null) {
                return redirect()
                    ->route('following.edit', $followingEntry)
                    ->withInput()
                    ->withErrors(['username' => __('No se reconoce el texto. Usa el enlace del perfil o el @usuario.')]);
            }

            $username = mb_strtolower($username);
            $url = SocialNetwork::profileUrlForSlug($tiktok->slug, $username);

            $dup = FollowingEntry::query()
                ->where('user_id', $request->user()->id)
                ->where('entry_type', FollowingEntry::TYPE_EXTERNAL)
                ->where('social_network_id', $tiktok->id)
                ->whereRaw('LOWER(username) = ?', [$username])
                ->where('id', '!=', $followingEntry->id)
                ->exists();

            if ($dup) {
                return redirect()
                    ->route('following.edit', $followingEntry)
                    ->withInput()
                    ->withErrors(['username' => __('Ya tienes otra entrada con este usuario.')]);
            }

            $note = $validated['note'] ?? null;
            if (is_string($note) && trim($note) === '') {
                $note = null;
            }
            $label = $validated['label'] ?? null;
            if (is_string($label) && trim($label) === '') {
                $label = null;
            }

            $profile = TiktokProfileInfo::fetch($username);
            if ($profile === null) {
                return redirect()
                    ->route('following.edit', $followingEntry)
                    ->withInput()
                    ->withErrors(['username' => __('No se encontró la cuenta en TikTok o el servicio no respondió.')]);
            }

            $useCustomAvatar = $request->boolean('use_custom_avatar');

            $attrs = [
                'social_network_id' => $tiktok->id,
                'username' => $username,
                'url' => $url,
                'label' => $label,
                'note' => $note,
                'use_custom_avatar' => $useCustomAvatar,
                'remote_display_name' => filled($profile['nickname'] ?? null) ? $profile['nickname'] : $followingEntry->remote_display_name,
            ];
            if (! $useCustomAvatar) {
                $attrs['avatar_url'] = $profile['avatar_url'] ?? $followingEntry->avatar_url;
            }

            $followingEntry->update($attrs);

            $this->persistFollowerSnapshotFromTiktokProfile($followingEntry->fresh(), $profile);
        } else {
            $validated = $request->validate([
                'label' => ['nullable', 'string', 'max:255'],
                'note' => ['nullable', 'string', 'max:2000'],
                'use_custom_avatar' => ['sometimes', 'boolean'],
            ]);

            $note = $validated['note'] ?? null;
            if (is_string($note) && trim($note) === '') {
                $note = null;
            }
            $label = $validated['label'] ?? null;
            if (is_string($label) && trim($label) === '') {
                $label = null;
            }

            $useCustomAvatar = $request->boolean('use_custom_avatar');
            $payload = [
                'label' => $label,
                'note' => $note,
                'use_custom_avatar' => $useCustomAvatar,
            ];
            $creator = User::query()->find($followingEntry->platform_user_id);
            if ($creator !== null) {
                [$snap, $profile] = $this->platformFollowingSnapshotWithProfile(
                    $creator,
                    $useCustomAvatar,
                    $followingEntry->avatar_url
                );
                $payload = array_merge($payload, $snap);
                $followingEntry->update($payload);
                $this->persistFollowerSnapshotFromTiktokProfile($followingEntry->fresh(), $profile);
            } else {
                $followingEntry->update($payload);
            }
        }

        return redirect()
            ->route('following.index')
            ->with('success', __('Entrada actualizada.'));
    }

    public function destroy(Request $request, FollowingEntry $followingEntry): RedirectResponse
    {
        abort_unless($followingEntry->user_id === $request->user()->id, 403);

        $followingEntry->delete();

        return redirect()
            ->route('following.index')
            ->with('success', __('Entrada eliminada.'));
    }

    /**
     * @param \Illuminate\Support\Collection<int, FollowingEntry> $currentEntries
     * @return \Illuminate\Support\Collection<int, User>
     */
    private function searchPlatformCreators(User $viewer, string $q, $currentEntries)
    {
        $excludeIds = $currentEntries
            ->where('entry_type', FollowingEntry::TYPE_PLATFORM)
            ->pluck('platform_user_id')
            ->filter()
            ->push($viewer->id)
            ->unique()
            ->values()
            ->all();

        $like = '%'.str_replace(['%', '_'], ['\\%', '\\_'], $q).'%';

        $query = User::query()
            ->where('role', 'creator');

        if ($excludeIds !== []) {
            $query->whereNotIn('id', $excludeIds);
        }

        return $query
            ->where(function ($w) use ($like) {
                $w->where('name', 'like', $like)
                    ->orWhere('email', 'like', $like)
                    ->orWhereHas('socialAccounts', function ($sa) use ($like) {
                        $sa->where('username', 'like', $like)
                            ->orWhere('display_name', 'like', $like);
                    });
            })
            ->with(['socialAccounts.socialNetwork'])
            ->orderBy('name')
            ->limit(25)
            ->get();
    }

    private function tiktokNetwork(): ?SocialNetwork
    {
        return SocialNetwork::query()->where('slug', 'tiktok')->first();
    }

    /**
     * @param  array<string, mixed>|null  $profile
     */
    private function persistFollowerSnapshotFromTiktokProfile(FollowingEntry $entry, ?array $profile): void
    {
        if ($profile === null || $profile['follower_count'] === null) {
            return;
        }

        $entry->followerSnapshots()->create([
            'follower_count' => $profile['follower_count'],
            'following_count' => $profile['following_count'],
            'heart_count' => $profile['heart_count'],
            'recorded_at' => now(),
        ]);
    }

    private function syncExternalFollowingFromTiktok(FollowingEntry $entry, SocialNetwork $tiktok): void
    {
        if (! $entry->isExternal() || ! filled($entry->username)) {
            return;
        }

        $entry->social_network_id = $tiktok->id;

        $profile = TiktokProfileInfo::fetch((string) $entry->username);
        if ($profile === null) {
            $entry->save();

            return;
        }

        $fill = [
            'url' => SocialNetwork::profileUrlForSlug($tiktok->slug, (string) $entry->username),
        ];
        if (! $entry->use_custom_avatar) {
            $fill['avatar_url'] = $profile['avatar_url'] ?? $entry->avatar_url;
        }
        if (filled($profile['nickname'] ?? null)) {
            $fill['remote_display_name'] = $profile['nickname'];
        }
        $entry->fill($fill);

        if ($entry->isDirty()) {
            $entry->save();
        }

        $this->persistFollowerSnapshotFromTiktokProfile($entry, $profile);
    }

    private function syncPlatformFollowingEntryFromCreator(FollowingEntry $followingEntry): void
    {
        if (! $followingEntry->isPlatform() || ! $followingEntry->platform_user_id) {
            return;
        }

        $creator = User::query()->find($followingEntry->platform_user_id);
        if ($creator === null) {
            return;
        }

        [$snap, $profile] = $this->platformFollowingSnapshotWithProfile(
            $creator,
            (bool) $followingEntry->use_custom_avatar,
            $followingEntry->avatar_url
        );

        $followingEntry->fill($snap);
        if ($followingEntry->isDirty()) {
            $followingEntry->save();
        }

        $this->persistFollowerSnapshotFromTiktokProfile($followingEntry, $profile);
    }

    /**
     * @return array{0: array{url: string, avatar_url: string|null, remote_display_name?: string}, 1: array|null}
     */
    private function platformFollowingSnapshotWithProfile(User $creator, bool $useCustomAvatar, ?string $existingAvatarUrl): array
    {
        $creator->loadMissing(['socialAccounts.socialNetwork']);
        $url = $creator->creator_profile_url;
        $handle = $this->tiktokHandleForPlatformAvatar($creator);
        $profile = $handle !== null ? TiktokProfileInfo::fetch($handle) : null;

        if ($useCustomAvatar) {
            $avatarUrl = $existingAvatarUrl;
        } else {
            $avatarUrl = $profile['avatar_url'] ?? null;
            if ($avatarUrl === null) {
                $avatarUrl = $this->snapshotPlatformAvatarUrl($creator);
            }
        }

        $snap = [
            'url' => $url,
            'avatar_url' => $avatarUrl,
        ];
        if ($profile !== null && filled($profile['nickname'] ?? null)) {
            $snap['remote_display_name'] = $profile['nickname'];
        }

        return [$snap, $profile];
    }

    /**
     * Copia la URL de avatar del creador (foto subida o placeholder local) como último recurso.
     */
    private function snapshotPlatformAvatarUrl(User $creator): ?string
    {
        $url = $creator->avatar_url;
        if (! is_string($url) || $url === '') {
            return null;
        }

        return mb_substr($url, 0, 2048);
    }

    /**
     * Usuario de TikTok con el que pedir datos remotos (principal usable, o cualquier TikTok vinculado).
     */
    private function tiktokHandleForPlatformAvatar(User $creator): ?string
    {
        $creator->loadMissing(['socialAccounts.socialNetwork']);

        $primary = $creator->primaryTiktokAccount();
        if ($primary !== null && filled($primary->username)) {
            return (string) $primary->username;
        }

        $fallback = $creator->socialAccounts->first(function ($account) {
            return strtolower((string) ($account->socialNetwork?->slug ?? '')) === 'tiktok'
                && filled($account->username);
        });

        if ($fallback !== null) {
            return (string) $fallback->username;
        }

        return null;
    }
}
