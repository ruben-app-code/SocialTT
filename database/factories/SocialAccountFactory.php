<?php

namespace Database\Factories;

use App\Models\SocialNetwork;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SocialAccountFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'social_network_id' => SocialNetwork::factory(),
            'display_name' => fake()->optional(0.4)->words(2, true),
            'username' => fake()->userName(),
            'url' => fake()->url(),
            'current_status' => fake()->randomElement(['active', 'active', 'active', 'deleted', 'stolen', 'blocked']),
            'is_verified' => fake()->boolean(),
            'is_primary' => false,
            'last_checked_at' => fake()->dateTime(),
        ];
    }
}
