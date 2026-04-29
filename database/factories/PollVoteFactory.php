<?php

namespace Database\Factories;

use App\Models\PollOption;
use App\Models\PollVote;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<PollVote>
 */
class PollVoteFactory extends Factory
{
    protected $model = PollVote::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'poll_id' => \App\Models\Poll::factory(),
            'user_id' => User::factory(),
            'option_id' => PollOption::factory(),
            'voter_key' => fn () => 'guest:'.Str::uuid()->toString(),
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (PollVote $vote): void {
            $option = PollOption::query()->find($vote->option_id);
            if ($option && (int) $vote->poll_id !== (int) $option->poll_id) {
                $vote->poll_id = $option->poll_id;
                $vote->save();
            }
            if ($vote->user_id) {
                $vote->voter_key = 'user:'.$vote->user_id;
                $vote->save();
            }
        });
    }
}
