<?php

namespace Database\Factories;

use App\Enum\InspectionSlotStatus; // Adjust or remove if you use plain strings
use App\Models\InspectionLocation;
use App\Models\InspectionSlot;
use App\Models\Location;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<InspectionSlot>
 */
class InspectionSlotFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = InspectionSlot::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'dealer_id'       => User::factory(),
            'buyer_id'        => User::factory(),
            'vehicle_id'      => Vehicle::factory(),
            'location_id'     => InspectionLocation::factory(),
            'inspection_date' => fake()->dateTimeBetween('now', '+1 month')->format('Y-m-d'),
            'inspection_time' => fake()->randomElement(['09:00:00', '11:00:00', '14:00:00', '16:00:00']),
            'status'          => fake()->randomElement(['pending', 'confirmed', 'completed', 'cancelled']),
        ];
    }

    /**
     * Indicate that the inspection slot is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }

    /**
     * Indicate that the inspection slot is confirmed.
     */
    public function confirmed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'confirmed',
        ]);
    }

    /**
     * Indicate that the inspection slot is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
        ]);
    }
}
