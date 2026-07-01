<?php

use App\Enum\UserStatus;
use App\Models\User;
use App\Models\Verify;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;

uses(RefreshDatabase::class);

it('verifies the user email and generates a token', function () {
    Http::fake([
        '*' => Http::response([
            'status' => true,
            'message' => 'Success',
        ], 200),
    ]);

    $user = User::factory()->create([
        'email_verified_at' => null,
        'status' => UserStatus::PENDING->value,
    ]);

    $verify = Verify::factory()->create([
        'user_id' => $user->id,
        'token' => '123456',
        'expires_at' => now()->addMinutes(10),
    ]);

    $response = $this->postJson('/api/v1/auth/verify-otp', [
        'code' => $verify->token,
    ]);

    $response->assertOk()
        ->assertJsonFragment([
            'message' => 'Account verified successfully.',
        ])
        ->assertJsonPath('data.user.id', $user->id);

    $user->refresh();

    expect($user->email_verified_at)->not->toBeNull();
    expect($user->status)->toBe(UserStatus::ACTIVE->value);

    $this->assertDatabaseMissing('verifies', [
        'id' => $verify->id,
    ]);
});

it('returns an error when the verification code is invalid', function () {
    Http::fake([
        '*' => Http::response([
            'status' => false,
            'message' => 'Invalid or expired verification code',
        ], 200),
    ]);

    $user = User::factory()->create([
        'email_verified_at' => null,
        'status' => UserStatus::PENDING->value,
    ]);

    Verify::factory()->create([
        'user_id' => $user->id,
        'token' => '123456',
        'expires_at' => now()->addMinutes(10),
    ]);

    $response = $this->postJson('/api/v1/auth/verify-otp', [
        'code' => 'invalid',
    ]);

    $response->assertNotFound()
        ->assertJsonFragment([
            'message' => 'Invalid or expired verification code.',
        ]);
});
