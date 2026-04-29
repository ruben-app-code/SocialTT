<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Poll;
use App\Models\PollOption;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\Creator\PollOptionController
 */
final class PollOptionControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_displays_view(): void
    {
        $pollOptions = PollOption::factory()->count(3)->create();

        $response = $this->get(route('poll-options.index'));

        $response->assertOk();
        $response->assertViewIs('pollOption.index');
        $response->assertViewHas('pollOptions', $pollOptions);
    }


    #[Test]
    public function create_displays_view(): void
    {
        $response = $this->get(route('poll-options.create'));

        $response->assertOk();
        $response->assertViewIs('pollOption.create');
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\Creator\PollOptionController::class,
            'store',
            \App\Http\Requests\PollOptionStoreRequest::class
        );
    }

    #[Test]
    public function store_saves_and_redirects(): void
    {
        $poll = Poll::factory()->create();
        $text = fake()->word();

        $response = $this->post(route('poll-options.store'), [
            'poll_id' => $poll->id,
            'text' => $text,
        ]);

        $pollOptions = PollOption::query()
            ->where('poll_id', $poll->id)
            ->where('text', $text)
            ->get();
        $this->assertCount(1, $pollOptions);
        $pollOption = $pollOptions->first();

        $response->assertRedirect(route('pollOptions.index'));
        $response->assertSessionHas('pollOption.id', $pollOption->id);
    }


    #[Test]
    public function show_displays_view(): void
    {
        $pollOption = PollOption::factory()->create();

        $response = $this->get(route('poll-options.show', $pollOption));

        $response->assertOk();
        $response->assertViewIs('pollOption.show');
        $response->assertViewHas('pollOption', $pollOption);
    }


    #[Test]
    public function edit_displays_view(): void
    {
        $pollOption = PollOption::factory()->create();

        $response = $this->get(route('poll-options.edit', $pollOption));

        $response->assertOk();
        $response->assertViewIs('pollOption.edit');
        $response->assertViewHas('pollOption', $pollOption);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\Creator\PollOptionController::class,
            'update',
            \App\Http\Requests\PollOptionUpdateRequest::class
        );
    }

    #[Test]
    public function update_redirects(): void
    {
        $pollOption = PollOption::factory()->create();
        $poll = Poll::factory()->create();
        $text = fake()->word();

        $response = $this->put(route('poll-options.update', $pollOption), [
            'poll_id' => $poll->id,
            'text' => $text,
        ]);

        $pollOption->refresh();

        $response->assertRedirect(route('pollOptions.index'));
        $response->assertSessionHas('pollOption.id', $pollOption->id);

        $this->assertEquals($poll->id, $pollOption->poll_id);
        $this->assertEquals($text, $pollOption->text);
    }


    #[Test]
    public function destroy_deletes_and_redirects(): void
    {
        $pollOption = PollOption::factory()->create();

        $response = $this->delete(route('poll-options.destroy', $pollOption));

        $response->assertRedirect(route('pollOptions.index'));

        $this->assertModelMissing($pollOption);
    }
}
