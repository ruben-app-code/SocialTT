<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use App\Http\Requests\Administrator\UpdateUserSocialAccountRequest;
use App\Models\SocialAccount;
use App\Models\SocialAccountBlock;
use App\Models\SocialNetwork;
use App\Models\User;
use App\Services\TiktokAvatarUrl;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class UserSocialAccountController extends Controller
{
    public function update(UpdateUserSocialAccountRequest $request, User $user, SocialAccount $socialAccount): RedirectResponse
    {
        abort_unless($socialAccount->user_id === $user->id, 404);

        $data = $request->validated();
        $topicIds = array_values(array_unique(array_filter(array_map('intval', $data['topics'] ?? []))));
        $blockedAtInput = $data['blocked_at'] ?? null;
        $blockDurationHours = $data['block_duration_hours'] ?? null;
        unset($data['blocked_at'], $data['block_duration_hours'], $data['topics']);

        $data['is_verified'] = $request->boolean('is_verified');

        $network = SocialNetwork::findOrFail($data['social_network_id']);
        $data['url'] = SocialNetwork::profileUrlForSlug($network->slug, $data['username']);
        $data['avatar_url'] = TiktokAvatarUrl::forNetworkSlug($network->slug, $data['username']);

        $userId = $user->id;
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

        DB::transaction(function () use ($socialAccount, $data, $userId, $oldNetworkId, $newNetworkId, $oldWasPrimary, $blockedAtInput, $blockDurationHours, $topicIds) {
            if ($oldNetworkId !== $newNetworkId && $oldWasPrimary) {
                SocialAccount::promoteFirstPrimaryCandidate($userId, $oldNetworkId, $socialAccount->id);
            }
            $socialAccount->update($data);
            if ($data['is_primary']) {
                SocialAccount::demoteOthersPrimary($userId, $newNetworkId, $socialAccount->id);
            }
            $socialAccount->topics()->sync($topicIds);
            $this->syncSocialAccountBlock($socialAccount->fresh(), $blockedAtInput, $blockDurationHours);
        });

        return redirect()
            ->route('users.edit', $user)
            ->with('success', __('Cuenta social actualizada.'));
    }

    public function destroy(User $user, SocialAccount $socialAccount): RedirectResponse
    {
        abort_unless($socialAccount->user_id === $user->id, 404);

        $wasPrimary = $socialAccount->is_primary;
        $userId = $socialAccount->user_id;
        $networkId = (int) $socialAccount->social_network_id;

        $socialAccount->delete();

        if ($wasPrimary) {
            SocialAccount::promoteFirstPrimaryCandidate($userId, $networkId);
        }

        return redirect()
            ->route('users.edit', $user)
            ->with('success', __('Cuenta social eliminada.'));
    }

    /**
     * @see \App\Http\Controllers\Creator\SocialAccountController::syncSocialAccountBlock
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
