<?php

namespace Database\Factories;

use App\Enum\AccidentType;
use App\Enum\ConditionType;
use App\Enum\DamageType;
use App\Enum\FuelType;
use App\Enum\ListingType;
use App\Enum\TransmissionType;
use App\Models\Country;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Vehicle>
 */
class VehicleFactory extends Factory
{
    protected $model = Vehicle::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $make = $this->faker->randomElement(['Toyota', 'Honda', 'Ford', 'BMW', 'Mercedes', 'Hyundai']);
        $modelName = $this->faker->word();
        $title = "{$make} {$modelName}";

        return [
            'user_id' => User::factory(),
            'slug' => Str::slug($title).'-'.$this->faker->unique()->numberBetween(1000, 9999),
            'auction_days' => $this->faker->randomElement([3, 5, 7, 10]),
            'auction_start_date' => now(),
            'auction_end_date' => now()->addDays(7),
            'country_id' => Country::factory(),
            'city' => $this->faker->city(),
            'listing_type' => ListingType::AUCTION->value,
            'fuel_type' => FuelType::PETROL->value,
            'transmission_type' => TransmissionType::AUTOMATIC->value,
            'condition' => ConditionType::USED->value,
            'accident_history' => AccidentType::NO_ACCIDENT->value,
            'damage_history' => DamageType::NO_DAMAGE->value,
            'kilometer_reading' => $this->faker->numberBetween(1000, 180000),
            'engine_capacity' => $this->faker->randomElement(['1.8L', '2.0L', '2.5L', '3.0L', '4.5L']),
            'previous_owner' => $this->faker->randomElement(['1', '2', '3+']),
            'make' => $make,
            'model' => $modelName,
            'year' => $this->faker->year(),
            'variant' => $this->faker->randomElement(['LE', 'XLE', 'SE', 'Standard', 'Premium']),
            'body_type' => $this->faker->randomElement(['Sedan', 'SUV', 'Hatchback', 'Coupe', 'Truck']),
            'vin' => strtoupper($this->faker->bothify('1FA6P8CF?H######')),
            'service_history' => $this->faker->randomElement(['Full Dealership History', 'Partial History', 'No History']),
            'front_image' => 'vehicles/default-front.jpg',
            'back_image' => 'vehicles/default-back.jpg',
            'rear_image' => 'vehicles/default-rear.jpg',
            'passenger_side_image' => 'vehicles/default-side.jpg',
            'dashboard_image' => 'vehicles/default-dash.jpg',
            'video_link' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'reserved_price' => $this->faker->randomFloat(2, 5000, 25000),
            'price' => $this->faker->randomFloat(2, 6000, 35000),
            'description' => $this->faker->paragraph(3),
            'features' => [
                'Air Conditioning',
                'Leather Seats',
                'Sunroof',
                'Navigation System',
                'Backup Camera',
            ],
        ];
    }
}
