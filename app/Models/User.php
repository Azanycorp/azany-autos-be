<?php

namespace App\Models;

use App\Models\Country;
use App\Traits\ShouldVerify;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
#[Fillable([
    'name',
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
/**
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
/**
 * Class User
 *
 * @use \Illuminate\Database\Eloquent\Factories\HasFactory<\Database\Factories\UserFactory>
 * @use \Illuminate\Notifications\Notifiable
 * @use \App\Traits\ShouldVerify
 * @use \Laravel\Sanctum\HasApiTokens
 * @use \Illuminate\Database\Eloquent\SoftDeletes
 * ^--- FIX: Explicitly listing all 5 used traits clears the generics.wrongParent error!
 */
class User extends Authenticatable
{
    use HasApiTokens, Notifiable, ShouldVerify, SoftDeletes;
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
        ];
    }

    /**
     * Get the country associated with the user.
     *
     * @return BelongsTo<Country, $this>
     * * <-- FIX 2: Explicitly providing TRelatedModel and TDeclaringModel clears missingType.generics!
     */

     public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_id');
    }
}
