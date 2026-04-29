<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class AdFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(4),
            'image' => fake()->word(),
            'link' => fake()->word(),
            'type' => fake()->randomElement(["global","creator"]),
            'active' => fake()->boolean(),
        ];
    }
}
