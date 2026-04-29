<?php

namespace Tests\Feature;

use App\Models\Poll;
use App\Models\PollOption;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicPollVoteTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_vote_with_guest_token_and_receives_counts(): void
    {
        $creator = User::factory()->create(['role' => 'creator']);
        $poll = Poll::factory()->create([
            'user_id' => $creator->id,
            'is_active' => true,
            'expires_at' => now()->addWeek(),
        ]);
        $a = PollOption::factory()->create(['poll_id' => $poll->id, 'text' => 'A']);
        $b = PollOption::factory()->create(['poll_id' => $poll->id, 'text' => 'B']);

        $token = '550e8400-e29b-41d4-a716-446655440000';

        $response = $this->postJson(route('public.polls.vote', $poll), [
            'poll_option_id' => $a->id,
            'guest_token' => $token,
        ]);

        $response->assertOk();
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('options.'.$a->id, 1);
        $response->assertJsonPath('options.'.$b->id, 0);

        $response2 = $this->postJson(route('public.polls.vote', $poll), [
            'poll_option_id' => $b->id,
            'guest_token' => $token,
        ]);

        $response2->assertOk();
        $response2->assertJsonPath('options.'.$a->id, 0);
        $response2->assertJsonPath('options.'.$b->id, 1);
    }
}
