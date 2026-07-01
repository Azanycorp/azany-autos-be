<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Verify;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Verify>
 */
class VerifyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'token' => fake()->sha256(),
            'email' => fake()->email(),
            'expires_at' => now()->addMinutes(10)
        ];
    }
}
