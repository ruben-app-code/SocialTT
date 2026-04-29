<?php

namespace Database\Factories;

use App\Models\SocialAccount;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class FollowerSnapshotFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'social_account_id' => SocialAccount::factory(),
            'followers_count' => fake()->numberBetween(0, 500000),
            'following_count' => fake()->optional(0.6)->numberBetween(0, 5000),
            'source' => fake()->randomElement(['manual', 'auto_prompt']),
            'recorded_at' => fake()->dateTime(),
        ];
    }
}
