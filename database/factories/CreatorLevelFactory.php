<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CreatorLevelFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'min_followers' => fake()->numberBetween(-10000, 10000),
            'max_followers' => fake()->numberBetween(-10000, 10000),
            'badge' => fake()->word(),
        ];
    }
}
