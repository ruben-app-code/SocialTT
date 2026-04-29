<?php

namespace Database\Factories;

use App\Models\SocialAccount;
use Illuminate\Database\Eloquent\Factories\Factory;

class SocialAccountEventFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'social_account_id' => SocialAccount::factory(),
            'type' => fake()->randomElement(["registered","verified","deleted","stolen","username_changed"]),
            'meta' => '{}',
        ];
    }
}
