<?php

use App\Enum\UserType;
use App\Models\Country;
use App\Models\User;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response as LaravelResponse;
use Illuminate\Support\Facades\Mail;

uses(RefreshDatabase::class);

beforeEach(function () {
    Mail::fake();
});

function registrationPayload(int $countryId): array
{
    return [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john@gmail.com',
        'password' => 'Password@123',
        'password_confirmation' => 'Password@123',
        'country_id' => $countryId,
        'user_type' => UserType::AUTOBUYER->value,
    ];
}

it('creates the user and returns a success message', function () {
    $country = Country::factory()->create();

    $payload = registrationPayload($country->id);

    $this->postJson('/api/v1/auth/register', $payload)
        ->assertOk()
        ->assertJsonFragment([
            'message' => 'Registration successful. Kindly check your inbox for instructions on how to verify your account. Thanks.',
        ]);

    $this->assertDatabaseHas('users', [
        'email' => 'john@gmail.com',
        'first_name' => 'John',
        'last_name' => 'Doe',
    ]);
});

it('fails validation when required fields are absent', function () {
    $this->postJson('/api/v1/auth/register', [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors([
            'first_name',
            'last_name',
            'email',
            'password',
            'country_id',
            'user_type',
        ]);
});

it('fails validation when the email is already taken', function () {
    User::factory()->create(['email' => 'john@gmail.com']);

    $country = Country::factory()->create();
    $payload = registrationPayload($country->id);

    $this->postJson('/api/v1/auth/register', $payload)
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['email']);
});

it('fails validation when country_id does not exist', function () {
    $country = Country::factory()->create();
    $payload = registrationPayload($country->id);

    $this->postJson('/api/v1/auth/register', array_merge($payload, ['country_id' => 99999]))
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['country_id']);
});

it('fails validation when user_type is not an allowed value', function () {
    $country = Country::factory()->create();
    $payload = registrationPayload($country->id);

    $this->postJson('/api/v1/auth/register', array_merge($payload, ['user_type' => 'superadmin']))
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['user_type']);
});

it('fails validation when password and confirmation do not match', function () {
    $country = Country::factory()->create();
    $payload = registrationPayload($country->id);

    $this->postJson('/api/v1/auth/register', array_merge($payload, [
        'password_confirmation' => 'DifferentPassword@99',
    ]))
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['password']);
});

it('rejects requests that contain unknown fields', function () {
    $country = Country::factory()->create();
    $payload = registrationPayload($country->id);

    $this->postJson('/api/v1/auth/register', array_merge($payload, ['rogue_field' => 'sneaky']))
        ->assertUnprocessable();
});