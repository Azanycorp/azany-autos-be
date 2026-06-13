<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;

uses(RefreshDatabase::class);

beforeEach(fn () => Mail::fake());

describe('POST /api/auth/login', function () {
    it('returns a token on valid credentials', function () {
        $user = User::factory()->create([
            'email' => 'john@gmail.com'
        ]);

        $this->postJson('/api/v1/auth/login', [
            'email' => $user->email,
            'password' => 'Password@123',
        ])
            ->assertOk()
            ->assertJsonPath('message', 'Token generated successfully.')
            ->assertJsonStructure([
                'data' => ['user', 'token'],
            ]);
    });

    it('returns 401 when the password is wrong', function () {

        $user = User::factory()->create([
            'email' => 'john@gmail.com'
        ]);

        $this->postJson('/api/v1/auth/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ])
            ->assertStatus(401)
            ->assertJsonPath('message', 'Credentials do not match');
    });

    it('returns 401 when the email is not registered', function () {
        $user = User::factory()->create([
            'email' => 'james@gmail.com',
            'password' => 'wrong-password',
        ]);

        $this->postJson('/api/v1/auth/login', [
            'email' => $user->email,
            'password' => $user->password,
        ])
            ->assertStatus(401)
            ->assertJsonPath('message', 'Credentials do not match');
    });

    it('does not issue a token when 2FA is enabled', function () {
        $user = User::factory()->create([
            'email' => 'john@gmail.com',
            'two_factor_enabled' => true,
        ]);

        $this->postJson('/api/v1/auth/login', [
            'email' => $user->email,
            'password' => 'Password@123',
        ])
            ->assertOk()
            ->assertJsonPath('message', '2FA code sent.')
            ->assertJsonMissing(['token']);
    });

    it('stores a verification code with an expiry when 2FA triggers', function () {
        $user = User::factory()->create([
            'email' => 'john@gmail.com',
        ]);

        $user->update(['two_factor_enabled' => true]);

        $this->postJson('/api/v1/auth/login', [
            'email' => $user->email,
            'password' => 'Password@123',
        ]);

        $fresh = $user->fresh();

        expect($fresh->verification_code)->not->toBeNull();
        expect($fresh->verification_code_expire_at)->not->toBeNull();
        expect($fresh->verification_code_expire_at->isFuture())->toBeTrue();
    });
});
