<?php

namespace Database\Factories;

use App\Models\CreatorLevel;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => Hash::make('password'),
            'timezone' => fake()->timezone(),
            'role' => fake()->randomElement(['creator', 'follower', 'admin']),
            'level_id' => null,
            'is_claimed' => fake()->boolean(30),
        ];
    }
}
