<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MessageFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'from_user_id' => User::factory(),
            'to_user_id' => User::factory(),
            'content' => fake()->paragraphs(3, true),
            'channel' => 'whatsapp',
            'status' => fake()->randomElement(['pending', 'sent', 'failed']),
        ];
    }
}
