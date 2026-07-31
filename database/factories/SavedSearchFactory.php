<?php

namespace Database\Factories;

use App\Models\SavedSearch;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SavedSearch>
 */
class SavedSearchFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = SavedSearch::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $minYear = fake()->numberBetween(2015, 2022);
        $minPrice = fake()->numberBetween(5000, 20000);

        return [
            'user_id'           => User::factory(),
            'name'              => fake()->words(3, true) . ' Search',
            'listing_type'      => fake()->randomElement(['sale', 'rent']),
            'fuel_type'         => fake()->randomElement(['petrol', 'diesel', 'electric', 'hybrid']),
            'transmission_type' => fake()->randomElement(['automatic', 'manual']),
            'condition'         => fake()->randomElement(['new', 'used']),
            'kilometer_reading' => fake()->numberBetween(10000, 150000),
            'make'              => fake()->randomElement(['Toyota', 'Honda', 'Ford', 'BMW', 'Mercedes']),
            'model'             => fake()->word(),
            'min_year'          => $minYear,
            'max_year'          => $minYear + fake()->numberBetween(1, 4),
            'min_price'         => $minPrice,
            'max_price'         => $minPrice + fake()->numberBetween(5000, 15000),
            'country_id'        => 1,
            'body_type'         => fake()->randomElement(['SUV', 'Sedan', 'Hatchback', 'Coupe']),
            'filters'           => [
                'color' => fake()->safeColorName(),
            ],
            'is_notify'         => fake()->boolean(80),
        ];
    }

    /**
     * Indicate that notification alerts are enabled.
     */
    public function notifyEnabled(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_notify' => true,
        ]);
    }

    /**
     * Indicate that notification alerts are paused/disabled.
     */
    public function notifyPaused(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_notify' => false,
        ]);
    }
}
