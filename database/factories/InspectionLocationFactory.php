<?php

namespace Database\Factories;

use App\Models\Country;
use App\Models\InspectionLocation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<InspectionLocation>
 */
class InspectionLocationFactory extends Factory
{
    protected $model = InspectionLocation::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => $this->faker->company().' Location one',
            'address' => $this->faker->streetAddress(),
            'city' => $this->faker->city(),
            'state' => $this->faker->state(),
            'note' => $this->faker->sentence(),
            'country_id' => Country::factory(),
            'is_default' => $this->faker->boolean(false),
        ];
    }
}
