<?php

namespace Database\Factories;

use App\Models\Country;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Country>
 */
class CountryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'sortname' => fake()->countryCode,
            'name' => fake()->country,
            'phonecode' => fake()->numberBetween(1, 999),
            'is_allowed' => fake()->boolean,
            'currency_code' => fake()->currencyCode,
            'flag' => fake()->imageUrl,
            'continent' => fake()->country,
        ];
    }
}
