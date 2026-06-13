<?php

namespace App\Models;

use App\Models\Country;
use App\Traits\ShouldVerify;
use App\Traits\UserRelationships;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property int $id
 * @property string $email
 * @property \Carbon\CarbonImmutable|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property string $first_name
 * @property string|null $last_name
 * @property int $country_id
 * @property string|null $status
 * @property string|null $user_type
 * @property string|null $phone
 * @property string|null $reg_number
 * @property string|null $business_name
 * @property string|null $contact_person
 * @property string|null $verification_code
 * @property \Carbon\CarbonImmutable|null $verification_code_expire_at
 * @property string|null $profile_photo
 * @property string|null $state
 * @property string|null $city
 * @property string|null $address
 * @property string|null $zip_code
 * @property bool $two_factor_enabled
 * @property bool $kyc_verification
 * @property bool $biometric_enabled
 * @property bool $lock_screen_enabled
 * @property \Carbon\CarbonImmutable|null $deleted_at
 * @property-read Country|null $country
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereBiometricEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereBusinessName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereContactPerson($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCountryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereKycVerification($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereLockScreenEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereProfilePhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRegNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTwoFactorEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUserType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereVerificationCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereVerificationCodeExpireAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereZipCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutTrashed()
 * @mixin \Eloquent
 */
#[Fillable([
    'email',
    'password',
    'first_name',
    'last_name',
    'country_id',
    'status',
    'user_type',
    'phone',
    'reg_number',
    'business_name',
    'contact_person',
    'verification_code',
    'verification_code_expire_at',
    'profile_photo',
    'state',
    'city',
    'address',
    'zip_code',
    'two_factor_enabled',
    'kyc_verification',
    'biometric_enabled',
    'email_verified_at',
    'lock_screen_enabled',
])]
#[Hidden(['password', 'remember_token'])]

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;

    use HasApiTokens, Notifiable, ShouldVerify, SoftDeletes, UserRelationships;
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_enabled' => 'boolean',
            'kyc_verification' => 'boolean',
            'biometric_enabled' => 'boolean',
            'lock_screen_enabled' => 'boolean',
            'verification_code_expire_at' => 'datetime',
        ];
    }
}
