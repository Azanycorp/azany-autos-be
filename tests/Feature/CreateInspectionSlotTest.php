<?php

use App\Enum\UserType;
use App\Models\InspectionLocation;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

it('an authenticated user can successfully add inspection slot', function () {

    $vehicle = Vehicle::factory()->create();
    $location = InspectionLocation::factory()->create();
    $user = User::factory()->create(['user_type' => UserType::AUTODEALER->value]);

    actingAs($user, 'sanctum');

    $payload = [
        'vehicle_id' => $vehicle->id,
        'location_id' => $location->id,
        'inspection_date' => '2026-08-22',
        'inspection_time' => '10:00 PM',
    ];

    $response = $this->postJson('/api/v1/dealer/inspections/add', $payload);

    $response->assertStatus(200)
        ->assertJsonStructure([
            'status',
            'message',
            'data' => [
                'id',
                'inspection_date',
                'inspection_time',
                'status',
                'vehicle' => [
                    'make',
                    'model',
                    'year',
                ],
                'location' => [
                    'name',
                    'address',
                ],
            ],
        ])
        ->assertJsonFragment([
            'status' => true,
            'message' => 'New Slot added successfully',
        ]);

    $this->assertDatabaseHas('inspection_slots', [
        'dealer_id' => $user->id,
        'vehicle_id' => $vehicle->id,
        'inspection_date' => '2026-08-22',
    ]);
});
