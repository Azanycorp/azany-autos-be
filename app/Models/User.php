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
 * @property \Carbon\CarbonInterface|null $email_verified_at
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
        ];
    }
}
