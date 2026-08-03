<?php

use App\Enum\UserStatus;
use App\Enum\VehicleStatus;
use App\Models\InspectionSlot;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('successfully retrieves user dashboard statistics and listing activity', function () {
    $user = User::factory()->create(['status' => UserStatus::ACTIVE->value]);

    Vehicle::factory()->count(2)->create([
        'user_id' => $user->id,
        'status' => VehicleStatus::ACTIVE->value,
    ]);

    Vehicle::factory()->create([
        'user_id' => $user->id,
        'status' => VehicleStatus::PENDING->value,
    ]);

    InspectionSlot::factory()->count(3)->create([
        'dealer_id' => $user->id,
    ]);

    $response = $this->actingAs($user)->getJson("api/v1/dealer/dashboard/{$user->id}");

    $response->assertOk()
        ->assertJsonFragment(['message' => 'Dashboard details'])
        ->assertJsonPath('data.active_listings', 2)
        ->assertJsonPath('data.pending_listings', 1)
        ->assertJsonPath('data.inspection_slots', 3)
        ->assertJsonCount(3, 'data.listing_activity');
});

it('returns empty counts when user has no listings or inspection slots', function () {
    $user = User::factory()->create(['status' => UserStatus::ACTIVE->value]);

    $response = $this->actingAs($user)->getJson("api/v1/dealer/dashboard/{$user->id}");

    $response->assertOk()
        ->assertJsonFragment([
            'active_listings' => 0,
            'pending_listings' => 0,
            'inspection_slots' => 0,
        ])
        ->assertJsonCount(0, 'data.listing_activity');
});

it('returns 404 when dashboard is requested for non-existent user', function () {
    $user = User::factory()->create(['status' => UserStatus::ACTIVE->value]);

    $response = $this->actingAs($user)->getJson('api/v1/dealer/dashboard/999999');

    $response->assertStatus(404)
        ->assertJsonFragment(['message' => 'User not found']);
});
