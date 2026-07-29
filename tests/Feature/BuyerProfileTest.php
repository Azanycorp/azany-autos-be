<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create([
        'email' => 'developer@azany.com',
        'first_name' => 'John',
        'last_name' => 'Doe',
        'two_factor_enabled' => false,
    ]);
});

it('returns user profile successfully', function () {
    Sanctum::actingAs($this->user);

    $response = $this->getJson("/api/v1/buyer/profile/{$this->user->id}");

    $response->assertStatus(200)
        ->assertJson(['message' => 'User profile'])
        ->assertJsonStructure([
            'data' => ['id', 'first_name', 'last_name', 'email'],
        ]);
});

it('returns 404 when fetching non-existent user profile', function () {
    Sanctum::actingAs($this->user);

    $response = $this->getJson('/api/v1/buyer/profile/99999');

    $response->assertStatus(404)
        ->assertJson(['message' => 'User does not exist']);
});

it('updates user profile details successfully', function () {
    Sanctum::actingAs($this->user);

    $payload = [
        'first_name' => 'Jane',
        'last_name' => 'Smith',
        'email' => 'jane.smith@azany.com',
        'country_id' => 1,
    ];

    $response = $this->postJson('/api/v1/buyer/update-profile', $payload);

    $response->assertStatus(200)
        ->assertJson(['message' => 'Details Updated']);

    $this->assertDatabaseHas('users', [
        'id' => $this->user->id,
        'first_name' => 'Jane',
        'last_name' => 'Smith',
        'email' => 'jane.smith@azany.com',
        'country_id' => 1,
    ]);
});

it('updates user profile photo successfully', function () {
    Storage::fake('public');
    Sanctum::actingAs($this->user);

    $file = UploadedFile::fake()->image('avatar.jpg');

    $response = $this->postJson('/api/v1/buyer/profile-photo', [
        'profile_photo' => $file,
    ]);

    $response->assertStatus(200)
        ->assertJson(['message' => 'Profile photo Updated']);

    $this->user->refresh();
    expect($this->user->profile_photo)->not->toBeNull();
});

it('updates user password successfully', function () {
    Sanctum::actingAs($this->user);

    $payload = [
        'password' => 'NewSecurePassword123!',
        'password_confirmation' => 'NewSecurePassword123!',
    ];

    $response = $this->postJson('/api/v1/buyer/update-password', $payload);

    $response->assertStatus(200)
        ->assertJson(['message' => 'Password Updated']);

    $this->user->refresh();
    expect(Hash::check('NewSecurePassword123!', $this->user->password))->toBeTrue();
});

it('enables 2FA successfully', function () {
    Sanctum::actingAs($this->user);

    $response = $this->postJson('/api/v1/buyer/update-2fa', [
        'two_factor_enabled' => true,
    ]);

    $response->assertStatus(200)
        ->assertJson(['message' => '2FA enabled successfully.']);

    $this->assertDatabaseHas('users', [
        'id' => $this->user->id,
        'two_factor_enabled' => true,
    ]);
});

it('disables 2FA successfully', function () {
    $this->user->update(['two_factor_enabled' => true]);
    Sanctum::actingAs($this->user);

    $response = $this->postJson('/api/v1/buyer/update-2fa', [
        'two_factor_enabled' => false,
    ]);

    $response->assertStatus(200)
        ->assertJson(['message' => '2FA disabled successfully.']);

    $this->assertDatabaseHas('users', [
        'id' => $this->user->id,
        'two_factor_enabled' => false,
    ]);
});

it('returns 400 error if 2FA status is already set', function () {
    $this->user->update(['two_factor_enabled' => true]);
    Sanctum::actingAs($this->user);

    $response = $this->postJson('/api/v1/buyer/update-2fa', [
        'two_factor_enabled' => true,
    ]);

    $response->assertStatus(400)
        ->assertJson(['message' => '2FA is already enabled.']);
});

it('prevents unauthenticated users from accessing profile endpoints', function () {
    $this->getJson('/api/v1/buyer/profile/1')->assertStatus(401);
    $this->postJson('/api/v1/buyer/update-profile', [])->assertStatus(401);
    $this->postJson('/api/v1/buyer/profile-photo', [])->assertStatus(401);
    $this->postJson('/api/v1/buyer/update-password', [])->assertStatus(401);
    $this->postJson('/api/v1/buyer/update-2fa', [])->assertStatus(401);
});
