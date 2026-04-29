<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PollFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'question' => fake()->word(),
            'type' => fake()->randomElement(["yes_no","multiple"]),
            'is_active' => fake()->boolean(),
            'expires_at' => fake()->dateTime(),
        ];
    }
}
