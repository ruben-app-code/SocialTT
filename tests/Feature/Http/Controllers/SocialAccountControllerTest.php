<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\SocialAccount;
use App\Models\SocialNetwork;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\Creator\SocialAccountController
 */
final class SocialAccountControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_displays_view(): void
    {
        $user = User::factory()->create();
        $socialAccounts = SocialAccount::factory()->count(3)->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get(route('social-accounts.index'));

        $response->assertOk();
        $response->assertViewIs('creator.social-accounts.index');
        $response->assertViewHas('socialAccounts');
    }


    #[Test]
    public function create_displays_view(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get(route('social-accounts.create'));

        $response->assertOk();
        $response->assertViewIs('creator.social-accounts.create');
        $response->assertViewHas('selectedTopics');
        $response->assertViewHas('accountsPerNetwork');
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\Creator\SocialAccountController::class,
            'store',
            \App\Http\Requests\SocialAccountStoreRequest::class
        );
    }

    #[Test]
    public function store_saves_and_redirects(): void
    {
        $user = User::factory()->create();
        $social_network = SocialNetwork::factory()->create();
        $topic = Topic::factory()->create();
        $username = fake()->userName();
        $current_status = 'active';

        $response = $this->actingAs($user)->post(route('social-accounts.store'), [
            'social_network_id' => $social_network->id,
            'username' => $username,
            'current_status' => $current_status,
            'is_primary' => '0',
            'topic_ids' => (string) $topic->id,
        ]);

        $socialAccounts = SocialAccount::query()
            ->where('user_id', $user->id)
            ->where('social_network_id', $social_network->id)
            ->where('username', $username)
            ->where('current_status', $current_status)
            ->get();
        $this->assertCount(1, $socialAccounts);
        $socialAccount = $socialAccounts->first();
        $this->assertSame(
            SocialNetwork::profileUrlForSlug($social_network->slug, $username),
            $socialAccount->url
        );
        $socialAccount->load('topics');
        $this->assertTrue($socialAccount->topics->pluck('id')->contains($topic->id));
        $this->assertFalse($socialAccount->is_verified);
        $this->assertTrue($socialAccount->is_primary, 'La primera cuenta de una red debe ser principal.');

        $response->assertRedirect(route('social-accounts.index'));
        $response->assertSessionHas('socialAccount.id', $socialAccount->id);
    }


    #[Test]
    public function show_displays_view(): void
    {
        $user = User::factory()->create();
        $socialAccount = SocialAccount::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get(route('social-accounts.show', $socialAccount));

        $response->assertOk();
        $response->assertViewIs('creator.social-accounts.show');
        $response->assertViewHas('socialAccount', $socialAccount);
    }


    #[Test]
    public function edit_displays_view(): void
    {
        $user = User::factory()->create();
        $socialAccount = SocialAccount::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get(route('social-accounts.edit', $socialAccount));

        $response->assertOk();
        $response->assertViewIs('creator.social-accounts.edit');
        $response->assertViewHas('socialAccount', $socialAccount);
        $response->assertViewHas('accountsPerNetwork');
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\Creator\SocialAccountController::class,
            'update',
            \App\Http\Requests\SocialAccountUpdateRequest::class
        );
    }

    #[Test]
    public function update_redirects(): void
    {
        $user = User::factory()->create();
        $socialAccount = SocialAccount::factory()->create([
            'user_id' => $user->id,
            'is_verified' => true,
        ]);
        $social_network = SocialNetwork::factory()->create();
        $username = fake()->userName();
        $current_status = 'active';

        $response = $this->actingAs($user)->put(route('social-accounts.update', $socialAccount), [
            'social_network_id' => $social_network->id,
            'username' => $username,
            'current_status' => $current_status,
            'is_primary' => '1',
        ]);

        $socialAccount->refresh();

        $response->assertRedirect(route('social-accounts.index'));
        $response->assertSessionHas('socialAccount.id', $socialAccount->id);

        $this->assertEquals($user->id, $socialAccount->user_id);
        $this->assertEquals($social_network->id, $socialAccount->social_network_id);
        $this->assertEquals($username, $socialAccount->username);
        $this->assertEquals($current_status, $socialAccount->current_status);
        $this->assertTrue($socialAccount->is_verified, 'La verificación no se pierde al editar otros campos.');
        $this->assertSame(
            SocialNetwork::profileUrlForSlug($social_network->slug, $username),
            $socialAccount->url
        );
        $this->assertTrue($socialAccount->is_primary);
    }

    #[Test]
    public function store_second_account_on_same_network_demotes_previous_primary_when_marked_primary(): void
    {
        $user = User::factory()->create();
        $social_network = SocialNetwork::factory()->create();
        $first = SocialAccount::factory()->create([
            'user_id' => $user->id,
            'social_network_id' => $social_network->id,
            'is_primary' => true,
        ]);
        $username = fake()->userName();

        $this->actingAs($user)->post(route('social-accounts.store'), [
            'social_network_id' => $social_network->id,
            'username' => $username,
            'current_status' => 'active',
            'is_primary' => '1',
            'topic_ids' => '',
        ]);

        $first->refresh();
        $second = SocialAccount::query()
            ->where('user_id', $user->id)
            ->where('social_network_id', $social_network->id)
            ->where('username', $username)
            ->firstOrFail();

        $this->assertFalse($first->is_primary);
        $this->assertTrue($second->is_primary);
    }

    #[Test]
    public function destroy_promotes_another_account_when_primary_deleted(): void
    {
        $user = User::factory()->create();
        $social_network = SocialNetwork::factory()->create();
        $primary = SocialAccount::factory()->create([
            'user_id' => $user->id,
            'social_network_id' => $social_network->id,
            'is_primary' => true,
        ]);
        $other = SocialAccount::factory()->create([
            'user_id' => $user->id,
            'social_network_id' => $social_network->id,
            'is_primary' => false,
        ]);

        $this->actingAs($user)->delete(route('social-accounts.destroy', $primary));

        $other->refresh();
        $this->assertTrue($other->is_primary);
    }

    #[Test]
    public function verification_patch_toggles_is_verified(): void
    {
        $user = User::factory()->create();
        $account = SocialAccount::factory()->create(['user_id' => $user->id, 'is_verified' => false]);

        $this->actingAs($user)
            ->patch(route('social-accounts.verification', $account), ['is_verified' => '1'])
            ->assertRedirect(route('social-accounts.index'))
            ->assertSessionHas('status');

        $this->assertTrue($account->fresh()->is_verified);

        $this->actingAs($user)
            ->patch(route('social-accounts.verification', $account), ['is_verified' => '0'])
            ->assertRedirect(route('social-accounts.index'));

        $this->assertFalse($account->fresh()->is_verified);
    }

    #[Test]
    public function destroy_deletes_and_redirects(): void
    {
        $user = User::factory()->create();
        $socialAccount = SocialAccount::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->delete(route('social-accounts.destroy', $socialAccount));

        $response->assertRedirect(route('social-accounts.index'));

        $this->assertModelMissing($socialAccount);
    }
}
