<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Poll;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\Creator\PollController
 */
final class PollControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_displays_view(): void
    {
        $polls = Poll::factory()->count(3)->create();

        $response = $this->get(route('polls.index'));

        $response->assertOk();
        $response->assertViewIs('poll.index');
        $response->assertViewHas('polls', $polls);
    }


    #[Test]
    public function create_displays_view(): void
    {
        $response = $this->get(route('polls.create'));

        $response->assertOk();
        $response->assertViewIs('poll.create');
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\Creator\PollController::class,
            'store',
            \App\Http\Requests\PollStoreRequest::class
        );
    }

    #[Test]
    public function store_saves_and_redirects(): void
    {
        $user = User::factory()->create();
        $question = fake()->word();
        $type = fake()->randomElement(/** enum_attributes **/);
        $is_active = fake()->boolean();

        $response = $this->post(route('polls.store'), [
            'user_id' => $user->id,
            'question' => $question,
            'type' => $type,
            'is_active' => $is_active,
        ]);

        $polls = Poll::query()
            ->where('user_id', $user->id)
            ->where('question', $question)
            ->where('type', $type)
            ->where('is_active', $is_active)
            ->get();
        $this->assertCount(1, $polls);
        $poll = $polls->first();

        $response->assertRedirect(route('polls.index'));
        $response->assertSessionHas('poll.id', $poll->id);
    }


    #[Test]
    public function show_displays_view(): void
    {
        $poll = Poll::factory()->create();

        $response = $this->get(route('polls.show', $poll));

        $response->assertOk();
        $response->assertViewIs('poll.show');
        $response->assertViewHas('poll', $poll);
    }


    #[Test]
    public function edit_displays_view(): void
    {
        $poll = Poll::factory()->create();

        $response = $this->get(route('polls.edit', $poll));

        $response->assertOk();
        $response->assertViewIs('poll.edit');
        $response->assertViewHas('poll', $poll);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\Creator\PollController::class,
            'update',
            \App\Http\Requests\PollUpdateRequest::class
        );
    }

    #[Test]
    public function update_redirects(): void
    {
        $poll = Poll::factory()->create();
        $user = User::factory()->create();
        $question = fake()->word();
        $type = fake()->randomElement(/** enum_attributes **/);
        $is_active = fake()->boolean();

        $response = $this->put(route('polls.update', $poll), [
            'user_id' => $user->id,
            'question' => $question,
            'type' => $type,
            'is_active' => $is_active,
        ]);

        $poll->refresh();

        $response->assertRedirect(route('polls.index'));
        $response->assertSessionHas('poll.id', $poll->id);

        $this->assertEquals($user->id, $poll->user_id);
        $this->assertEquals($question, $poll->question);
        $this->assertEquals($type, $poll->type);
        $this->assertEquals($is_active, $poll->is_active);
    }


    #[Test]
    public function destroy_deletes_and_redirects(): void
    {
        $poll = Poll::factory()->create();

        $response = $this->delete(route('polls.destroy', $poll));

        $response->assertRedirect(route('polls.index'));

        $this->assertModelMissing($poll);
    }
}
