<?php

namespace Tests\Feature;

use App\Enum\AccidentType;
use App\Enum\ConditionType;
use App\Enum\DamageType;
use App\Enum\FuelType;
use App\Enum\ListingType;
use App\Enum\TransmissionType;
use App\Enum\UserType;
use App\Enum\VehicleStatus;
use App\Models\Country;
use App\Models\User;
use App\Models\Vehicle;
use CloudinaryLabs\CloudinaryLaravel\CloudinaryEngine;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Mockery;
use Tests\TestCase;

class CreateVehicleTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_an_authenticated_user_can_successfully_add_a_vehicle()
    {
        Storage::fake('vehicles');
        Storage::fake('vehicle_images');

        $mockedUploadResult = Mockery::mock(CloudinaryEngine::class);
        $mockedUploadResult->shouldReceive('getSecurePath')
            ->andReturn('https://res.cloudinary.com/fake-account/image/upload/v123/vehicles/fake_car.jpg');
        $mockedUploadResult->shouldReceive('getPublicId')
            ->andReturn('vehicles/fake_car');

        Cloudinary::shouldReceive('upload')
            ->andReturn($mockedUploadResult);

        $country = Country::factory()->create();
        $user = User::factory()->create(['user_type' => UserType::AUTODEALER->value]);
        Sanctum::actingAs($user, ['*']);
        $payload = [
            'make' => 'Toyota',
            'model' => 'Camry',
            'year' => '2024',
            'price' => 25000,
            'slug' => str()->slug("new vehicle-" . str()->random(6)),                   
            'listing_type' => ListingType::AUCTION->value,
            'status' => VehicleStatus::PENDING->value,
            'auction_days' => 7,
            'auction_start_date' => now(),
            'auction_end_date' => now()->addDays(7),
            'country_id' => $country->id,
            'city' => 'Lagos',
            'fuel_type' => FuelType::PETROL->value,
            'transmission_type' => TransmissionType::AUTOMATIC->value,
            'condition' => ConditionType::USED->value,
            'kilometer_reading' => '15000',
            'engine_capacity' => '2.5L',
            'previous_owner' => 'Sunday',
            'variant' => 'LE',
            'body_type' => 'Sedan',
            'vin' => '1HGCR2F8XHAXXXXXX',
            'accident_history' => AccidentType::NO_ACCIDENT->value,
            'damage_history' => DamageType::NO_DAMAGE->value,
            'service_history' => 'Full',
            'description' => 'A very clean vehicle.',
            'features' => ['Leather seats', 'Sunroof'],
            'front_image' => UploadedFile::fake()->image('front.jpg'),
            'back_image' => UploadedFile::fake()->image('back.jpg'),
            'rear_image' => UploadedFile::fake()->image('rear.jpg'),
            'passenger_side_image' => UploadedFile::fake()->image('passenger.jpg'),
            'dashboard_image' => UploadedFile::fake()->image('dashboard.jpg'),

            'vehicle_images' => [
                UploadedFile::fake()->image('extra1.jpg'),
                UploadedFile::fake()->image('extra2.jpg'),
            ],
        ];

        $response = $this->postJson('/api/v1/dealer/vehicles/add', $payload);

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'id', 'make', 'model', 'year', 'slug',
                ],
                'message',
            ])
            ->assertJsonFragment([
                'message' => 'Vehicle added successfully',
            ]);

        $this->assertDatabaseHas('vehicles', [
            'user_id' => $user->id,
            'make' => 'Toyota',
            'model' => 'Camry',
            'year' => '2024',
            'status' => VehicleStatus::PENDING->value,
        ]);

        $vehicle = Vehicle::first();

        $this->assertCount(2, $vehicle->vehicleImages);
    }
}
