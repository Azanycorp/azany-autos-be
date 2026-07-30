<?php

use App\Models\BuyerPreference;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();

    $this->vehicles = Vehicle::factory()->count(2)->create();

    $this->validPayload = [
        'vehicles' => ['Camry', 'Corolla'],
        'prefered_colors' => ['Black', 'White'],
        'body_types' => ['SUV', 'Sedan'],
        'fuel_types' => ['petrol', 'diesel'],
        'transmissions' => ['automatic', 'manual'],
        'budget_min' => 5000,
        'budget_max' => 25000,
    ];
});

it('retrieves vehicle preference successfully for authenticated user', function () {
    Sanctum::actingAs($this->user);

    BuyerPreference::create([
        'user_id' => $this->user->id,
        'vehicles' => ['Camry', 'Corolla'],
        'prefered_colors' => ['Black', 'White'],
        'body_types' => ['SUV', 'Sedan'],
        'fuel_types' => ['petrol', 'diesel'],
        'transmissions' => ['automatic', 'manual'],
        'budget_min' => 5000,
        'budget_max' => 25000,
    ]);

    $response = $this->getJson('/api/v1/buyer/get-preference');

    $response->assertStatus(200)
        ->assertJson([
            'message' => 'Preference retrieved successfully',
        ])
        ->assertJsonStructure([
            'data' => [
                'fuel_types',
                'budget_min',
                'budget_max',
                'prefered_colors',
                'transmissions',
                'body_types',
            ],
        ]);
});

it('creates a new vehicle preference successfully', function () {
    Sanctum::actingAs($this->user);

    $response = $this->postJson('/api/v1/buyer/set-preference', $this->validPayload);

    $response->assertStatus(200)
        ->assertJson(['message' => 'Preference set successfully']);

    $this->assertDatabaseHas('buyer_preferences', [
        'user_id' => $this->user->id,
        'budget_min' => 5000,
        'budget_max' => 25000,
    ]);
});

it('updates existing vehicle preference instead of creating duplicates', function () {
    Sanctum::actingAs($this->user);

    $this->postJson('/api/v1/buyer/set-preference', $this->validPayload);

    $updatedPayload = array_merge($this->validPayload, [
        'budget_min' => 10000,
        'budget_max' => 50000,
    ]);

    $response = $this->postJson('/api/v1/buyer/set-preference', $updatedPayload);

    $response->assertStatus(200)
        ->assertJson(['message' => 'Preference set successfully']);

    expect(BuyerPreference::where('user_id', $this->user->id)->count())->toBe(1);

    $this->assertDatabaseHas('buyer_preferences', [
        'user_id' => $this->user->id,
        'budget_min' => 10000,
        'budget_max' => 50000,
    ]);
});

it('fails validation when required preference fields are missing', function () {
    Sanctum::actingAs($this->user);

    $response = $this->postJson('/api/v1/buyer/set-preference', []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors([
            'vehicles',
            'prefered_colors',
            'body_types',
            'fuel_types',
            'transmissions',
            'budget_min',
            'budget_max',
        ]);
});

it('prevents unauthenticated users from accessing preference endpoints', function () {
    $this->getJson('/api/v1/buyer/get-preference')->assertStatus(401);
    $this->postJson('/api/v1/buyer/set-preference', $this->validPayload)->assertStatus(401);
});
