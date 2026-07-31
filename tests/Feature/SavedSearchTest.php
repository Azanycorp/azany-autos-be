<?php

use App\Enum\ConditionType;
use App\Enum\FuelType;
use App\Enum\ListingType;
use App\Enum\TransmissionType;
use App\Enum\UserStatus;
use App\Models\SavedSearch;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('retrieves saved searches and stats for a valid user', function () {
    $user = User::factory()->create(['status' => UserStatus::ACTIVE->value]);

    $response = $this->actingAs($user)->getJson("api/v1/buyer/saved-searches/{$user->id}");

    $response->assertOk()
        ->assertJsonStructure([
            'data' => [
                'stats' => [
                    'total_saved',
                    'alerts_on',
                    'alerts_paused',
                    'new_matches_today',
                    'searches_with_new_matches',
                    'total_matches_found',
                ],
                'searches',
            ],
        ]);
});

it('returns 404 when getting saved searches for non-existent user', function () {
    $user = User::factory()->create(['status' => UserStatus::ACTIVE->value]);

    $response = $this->actingAs($user)->getJson('api/v1/buyer/saved-searches/999999');

    $response->assertStatus(404)
        ->assertJsonFragment(['message' => 'User not found']);
});

/*
|--------------------------------------------------------------------------
| Add Saved Search
|--------------------------------------------------------------------------
*/

it('successfully adds a new saved search', function () {
    $user = User::factory()->create(['status' => UserStatus::ACTIVE->value]);

    $payload = [
        'user_id'           => $user->id,
        'name'              => 'Toyota SUV Search',
        'listing_type'      => ListingType::SALE->value,
        'fuel_type'         => FuelType::PETROL->value,
        'transmission_type' => TransmissionType::AUTOMATIC->value,
        'condition'         => ConditionType::NEW->value,
        'kilometer_reading' => 50000,
        'make'              => 'Toyota',
        'model'             => 'RAV4',
        'min_year'          => 2018,
        'max_year'          => 2024,
        'min_price'         => 10000,
        'max_price'         => 30000,
        'country_id'        => 1,
        'body_type'         => 'SUV',
    ];

    $response = $this->actingAs($user)->postJson('api/v1/buyer/saved-searches/add', $payload);

    $response->assertOk()
        ->assertJsonFragment(['message' => 'New record added successfully']);

    $this->assertDatabaseHas('saved_searches', [
        'user_id' => $user->id,
        'name'    => 'Toyota SUV Search',
        'make'    => 'Toyota',
    ]);
});

/*
|--------------------------------------------------------------------------
| View Saved Search Details
|--------------------------------------------------------------------------
*/

it('views a specific saved search detail', function () {
    $user = User::factory()->create(['status' => UserStatus::ACTIVE->value]);
    $savedSearch = SavedSearch::factory()->create([
        'user_id' => $user->id,
        'name'    => 'Honda Sedan',
    ]);

    $response = $this->actingAs($user)->getJson("api/v1/buyer/saved-searches/details/{$savedSearch->id}");

    $response->assertOk()
        ->assertJsonFragment(['name' => 'Honda Sedan']);
});

it('returns 404 when viewing non-existent or unowned saved search', function () {
    $user = User::factory()->create(['status' => UserStatus::ACTIVE->value]);
    $otherUser = User::factory()->create();

    $otherSearch = SavedSearch::factory()->create(['user_id' => $otherUser->id]);

    // Attempt to view another user's saved search
    $response = $this->actingAs($user)->getJson("api/v1/buyer/saved-searches/details/{$otherSearch->id}");

    $response->assertStatus(404)
        ->assertJsonFragment(['message' => 'Record not found']);
});

/*
|--------------------------------------------------------------------------
| Update Saved Search
|--------------------------------------------------------------------------
*/

it('updates an existing saved search record', function () {
    $user = User::factory()->create(['status' => UserStatus::ACTIVE->value]);
    $savedSearch = SavedSearch::factory()->create([
        'user_id' => $user->id,
        'name'    => 'Old Search Name',
    ]);

    $payload = [
        'name' => 'Updated Search Name',
    ];

    $response = $this->actingAs($user)->postJson("api/v1/buyer/saved-searches/update/{$savedSearch->id}", $payload);

    $response->assertOk();

    $this->assertDatabaseHas('saved_searches', [
        'id'   => $savedSearch->id,
        'name' => 'Updated Search Name',
    ]);
});

/*
|--------------------------------------------------------------------------
| Delete Saved Search
|--------------------------------------------------------------------------
*/

it('deletes a saved search record', function () {
    $user = User::factory()->create(['status' => UserStatus::ACTIVE->value]);
    $savedSearch = SavedSearch::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->deleteJson("api/v1/buyer/saved-searches/delete/{$savedSearch->id}");

    $response->assertOk()
        ->assertJsonFragment(['message' => 'Record deleted']);

    $this->assertDatabaseMissing('saved_searches', ['id' => $savedSearch->id]);
});

/*
|--------------------------------------------------------------------------
| Run Saved Search
|--------------------------------------------------------------------------
*/

it('runs a saved search and returns matched results', function () {
    $user = User::factory()->create(['status' => UserStatus::ACTIVE->value]);
    $savedSearch = SavedSearch::factory()->create([
        'user_id' => $user->id,
        'name'    => 'Luxury Cars',
    ]);

    $response = $this->actingAs($user)->getJson("api/v1/buyer/saved-searches/run-search/{$savedSearch->id}");

    $response->assertOk()
        ->assertJsonFragment(['message' => "Matches found for 'Luxury Cars'"]);
});
