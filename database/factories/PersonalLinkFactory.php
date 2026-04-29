<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PersonalLink>
 */
class PersonalLinkFactory extends Factory
{
    protected $model = \App\Models\PersonalLink::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'label' => fake()->words(3, true),
            'url' => fake()->url(),
            'sort_order' => fake()->numberBetween(0, 100),
        ];
    }
}
