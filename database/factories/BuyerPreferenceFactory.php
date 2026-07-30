<?php

namespace Database\Factories;

use App\Models\BuyerPreference;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BuyerPreference>
 */
class BuyerPreferenceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<BuyerPreference>
     */
    protected $model = BuyerPreference::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'vehicles' => ['Camry', 'Corolla'],
            'fuel_types' => ['petrol', 'diesel'],
            'prefered_colors' => ['Black', 'White'],
            'transmissions' => ['automatic', 'manual'],
            'body_types' => ['SUV', 'Sedan'],
            'budget_min' => 5000,
            'budget_max' => 25000,
        ];
    }
}
