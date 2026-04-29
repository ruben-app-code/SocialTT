<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubscriptionFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'type' => fake()->randomElement(["free","pro","lifetime"]),
            'ads_enabled' => fake()->boolean(),
            'expires_at' => fake()->dateTime(),
        ];
    }
}
