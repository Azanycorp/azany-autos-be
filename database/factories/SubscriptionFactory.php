<?php

namespace Database\Factories;

use App\Enum\SubscriptionStatus;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Subscription>
 */
class SubscriptionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->create()->id,
            'subscription_plan_id' => SubscriptionPlan::factory()->create()->id,
            'amount' => fake()->randomFloat(),
            'starts_at' => fake()->dateTime(),
            'end_at' => fake()->dateTime(),
            'gateway' => fake()->text(),
            'status' => SubscriptionStatus::PENDING,
        ];
    }
}
