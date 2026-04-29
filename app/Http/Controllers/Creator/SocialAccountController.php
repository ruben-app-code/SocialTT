<?php

namespace App\Http\Controllers\Creator;

use App\Http\Controllers\Controller;

use App\Http\Requests\SocialAccountStoreRequest;
use App\Http\Requests\SocialAccountUpdateRequest;
use App\Models\SocialAccount;
use App\Models\SocialAccountBlock;
use App\Models\SocialNetwork;
use App\Models\Topic;
use App\Models\User;
use App\Services\TiktokAvatarUrl;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SocialAccountController extends Controller
{
    public function index(Request $request): View
    {
        $socialAccounts = $request->user()->socialAccounts()->with(['socialNetwork', 'topics', 'block'])->get();

        return view('creator.social-accounts.index', [
            'socialAccounts' => $socialAccounts,
        ]);
    }

    public function create(Request $request): View
    {
        $socialNetworks = SocialNetwork::orderBy('name')->get();
        $selectedTopics = $this->topicsFromCommaIdsString(old('topic_ids', ''));

        return view('creator.social-accounts.create', [
            'socialNetworks' => $socialNetworks,
            'selectedTopics' => $selectedTopics,
            'accountsPerNetwork' => $this->accountsPerNetworkForUser($request->user()),
        ]);
    }

    public function store(SocialAccountStoreRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $topicIds = $this->parseTopicIdsFromString($data['topic_ids'] ?? null);
        $blockedAtInput = $data['blocked_at'] ?? null;
        $blockDurationHours = $data['block_duration_hours'] ?? null;
        unset($data['topic_ids'], $data['blocked_at'], $data['block_duration_hours']);
        $data['user_id'] = $request->user()->id;
        $data['is_verified'] = false;
        $network = SocialNetwork::findOrFail($data['social_network_id']);
        $data['url'] = SocialNetwork::profileUrlForSlug($network->slug, $data['username']);
        $data['avatar_url'] = TiktokAvatarUrl::forNetworkSlug($network->slug, $data['username']);

        $existingForNetwork = SocialAccount::query()
            ->where('user_id', $request->user()->id)
            ->where('social_network_id', $data['social_network_id'])
            ->count();

        if ($existingForNetwork === 0) {
            $data['is_primary'] = true;
        } else {
            $data['is_primary'] = $request->boolean('is_primary');
        }

        $socialAccount = DB::transaction(function () use ($data, $topicIds, $request, $blockedAtInput, $blockDurationHours) {
            if ($data['is_primary']) {
                SocialAccount::demoteOthersPrimary($request->user()->id, $data['social_network_id']);
            }
            $account = SocialAccount::create($data);
            $account->topics()->sync($topicIds);
            $this->syncSocialAccountBlock($account, $blockedAtInput, $blockDurationHours);

            return $account;
        });

        $request->session()->flash('socialAccount.id', $socialAccount->id);

        return redirect()->route('social-accounts.index');
    }

    public function show(Request $request, SocialAccount $socialAccount): View|RedirectResponse
    {
        if ($socialAccount->user_id !== $request->user()->id) {
            abort(403);
        }

        return view('creator.social-accounts.show', [
            'socialAccount' => $socialAccount->load(['socialNetwork', 'topics.parent', 'block']),
        ]);
    }

    public function edit(Request $request, SocialAccount $socialAccount): View|RedirectResponse
    {
        if ($socialAccount->user_id !== $request->user()->id) {
            abort(403);
        }

        $socialNetworks = \App\Models\SocialNetwork::orderBy('name')->get();

        return view('creator.social-accounts.edit', [
            'socialAccount' => $socialAccount->load(['topics.parent']),
            'socialNetworks' => $socialNetworks,
            'accountsPerNetwork' => $this->accountsPerNetworkForUser($request->user()),
        ]);
    }

    public function update(SocialAccountUpdateRequest $request, SocialAccount $socialAccount): RedirectResponse
    {
        if ($socialAccount->user_id !== $request->user()->id) {
            abort(403);
        }

        $data = $request->validated();
        $blockedAtInput = $data['blocked_at'] ?? null;
        $blockDurationHours = $data['block_duration_hours'] ?? null;
        unset($data['blocked_at'], $data['block_duration_hours']);

        $network = SocialNetwork::findOrFail($data['social_network_id']);
        $data['url'] = SocialNetwork::profileUrlForSlug($network->slug, $data['username']);
        $data['avatar_url'] = TiktokAvatarUrl::forNetworkSlug($network->slug, $data['username']);

        $userId = $request->user()->id;
        $oldNetworkId = (int) $socialAccount->social_network_id;
        $oldWasPrimary = $socialAccount->is_primary;
        $newNetworkId = (int) $data['social_network_id'];

        $othersOnNew = SocialAccount::query()
            ->where('user_id', $userId)
            ->where('social_network_id', $newNetworkId)
            ->where('id', '!=', $socialAccount->id)
            ->count();

        if ($othersOnNew === 0) {
            $data['is_primary'] = true;
        } else {
            $data['is_primary'] = $request->boolean('is_primary');
        }

        DB::transaction(function () use ($socialAccount, $data, $userId, $oldNetworkId, $newNetworkId, $oldWasPrimary, $blockedAtInput, $blockDurationHours) {
            if ($oldNetworkId !== $newNetworkId && $oldWasPrimary) {
                SocialAccount::promoteFirstPrimaryCandidate($userId, $oldNetworkId, $socialAccount->id);
            }
            $socialAccount->update($data);
            if ($data['is_primary']) {
                SocialAccount::demoteOthersPrimary($userId, $newNetworkId, $socialAccount->id);
            }
            $this->syncSocialAccountBlock($socialAccount->fresh(), $blockedAtInput, $blockDurationHours);
        });

        $request->session()->flash('socialAccount.id', $socialAccount->id);

        return redirect()->route('social-accounts.index');
    }

    /**
     * Marca o desmarca la cuenta como verificada (solo desde el listado; indicador manual del creador).
     */
    public function updateVerification(Request $request, SocialAccount $socialAccount): RedirectResponse
    {
        if ($socialAccount->user_id !== $request->user()->id) {
            abort(403);
        }

        $validated = $request->validate([
            'is_verified' => ['required', 'boolean'],
        ]);

        $socialAccount->update(['is_verified' => $validated['is_verified']]);

        return redirect()
            ->route('social-accounts.index')
            ->with('status', $validated['is_verified']
                ? __('Cuenta marcada como verificada.')
                : __('Verificación retirada.'));
    }

    public function destroy(Request $request, SocialAccount $socialAccount): RedirectResponse
    {
        if ($socialAccount->user_id !== $request->user()->id) {
            abort(403);
        }

        $wasPrimary = $socialAccount->is_primary;
        $userId = $socialAccount->user_id;
        $networkId = (int) $socialAccount->social_network_id;

        $socialAccount->delete();

        if ($wasPrimary) {
            SocialAccount::promoteFirstPrimaryCandidate($userId, $networkId);
        }

        return redirect()->route('social-accounts.index');
    }

    /**
     * Vista previa de la URL del perfil (misma lógica que al guardar).
     */
    public function profileUrlPreview(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'social_network_id' => ['nullable', 'integer', 'exists:social_networks,id'],
            'username' => ['nullable', 'string', 'max:255'],
        ]);

        if (empty($validated['social_network_id'])) {
            return response()->json(['url' => '']);
        }

        $network = SocialNetwork::find($validated['social_network_id']);

        return response()->json([
            'url' => SocialNetwork::profileUrlForSlug(
                $network->slug,
                (string) ($validated['username'] ?? '')
            ),
        ]);
    }

    /**
     * Búsqueda de temas por nombre (autocompletado del formulario de cuenta).
     */
    public function searchTopics(Request $request): JsonResponse
    {
        $q = trim((string) $request->query('q', ''));
        if (mb_strlen($q) < 3) {
            return response()->json(['topics' => []]);
        }

        $like = '%'.str_replace(['%', '_'], ['\\%', '\\_'], $q).'%';
        $topics = Topic::query()
            ->with('parent:id,name')
            ->where('name', 'like', $like)
            ->orderBy('name')
            ->limit(30)
            ->get()
            ->map(fn (Topic $t) => [
                'id' => $t->id,
                'name' => $t->display_name,
            ]);

        return response()->json(['topics' => $topics]);
    }

    /**
     * Asocia un tema a la cuenta (edición en vivo).
     */
    public function attachTopic(Request $request, SocialAccount $socialAccount): JsonResponse
    {
        if ($socialAccount->user_id !== $request->user()->id) {
            abort(403);
        }

        $validated = $request->validate([
            'topic_id' => ['required', 'integer', 'exists:topics,id'],
        ]);

        $socialAccount->topics()->syncWithoutDetaching([$validated['topic_id']]);

        return response()->json(['ok' => true]);
    }

    /**
     * Quita un tema de la cuenta (edición en vivo).
     */
    public function detachTopic(Request $request, SocialAccount $socialAccount, Topic $topic): JsonResponse
    {
        if ($socialAccount->user_id !== $request->user()->id) {
            abort(403);
        }

        $socialAccount->topics()->detach($topic->id);

        return response()->json(['ok' => true]);
    }

    /**
     * Conteo de cuentas por red (ids de red como string clave, para el JS del formulario).
     *
     * @return Collection<string, int>
     */
    private function accountsPerNetworkForUser(User $user): Collection
    {
        return $user->socialAccounts()
            ->pluck('social_network_id')
            ->countBy()
            ->mapWithKeys(fn (int $count, $id) => [(string) $id => $count]);
    }

    /**
     * @return array<int, int>
     */
    private function parseTopicIdsFromString(?string $raw): array
    {
        if ($raw === null || trim($raw) === '') {
            return [];
        }

        $ids = array_unique(array_filter(array_map('intval', explode(',', $raw))));
        if ($ids === []) {
            return [];
        }

        return Topic::query()->whereIn('id', $ids)->pluck('id')->all();
    }

    /**
     * @return \Illuminate\Support\Collection<int, Topic>
     */
    private function topicsFromCommaIdsString(?string $raw)
    {
        $ids = $this->parseTopicIdsFromString($raw);
        if ($ids === []) {
            return collect();
        }

        return Topic::query()->with('parent')->whereIn('id', $ids)->get();
    }

    /**
     * Tabla social_account_blocks (1:1). Si no está bloqueada, elimina el registro.
     */
    private function syncSocialAccountBlock(SocialAccount $account, mixed $blockedAtInput, mixed $blockDurationHours): void
    {
        if ($account->current_status !== 'blocked') {
            SocialAccountBlock::query()->where('social_account_id', $account->id)->delete();

            return;
        }

        $defaultHours = (int) config('social_accounts.default_block_duration_hours', 24);
        $hours = ($blockDurationHours !== null && $blockDurationHours !== '')
            ? max(1, min(8760, (int) $blockDurationHours))
            : max(1, min(8760, $defaultHours));

        $tz = config('app.timezone');
        $blockedAt = $blockedAtInput
            ? Carbon::parse($blockedAtInput, $tz)
            : now($tz);

        $activatesAt = $blockedAt->copy()->addHours($hours);

        SocialAccountBlock::query()->updateOrCreate(
            ['social_account_id' => $account->id],
            [
                'blocked_at' => $blockedAt,
                'activates_at' => $activatesAt,
            ]
        );
    }
}