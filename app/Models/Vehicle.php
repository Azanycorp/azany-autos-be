<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'user_id',
    'slug',
    'status',
    'listing_type',
    'country_id',
    'city',
    'fuel_type',
    'transmission_type',
    'condition',
    'kilometer_reading',
    'engine_capacity',
    'previous_owner',
    'make',
    'model',
    'year',
    'variant',
    'body_type',
    'vin',
    'accident_history',
    'damage_history',
    'service_history',
    'front_image',
    'back_image',
    'rear_image',
    'passenger_side_image',
    'dashboard_image',
    'video_link',
    'price',
    'description',
    'features',
])]
class Vehicle extends Model
{
    protected function casts(): array
    {
        return [
            'features' => 'array',
        ];
    }

    /**
     * @return HasMany<VehicleImage, $this>
     */
    public function vehicleImages(): HasMany
    {
        return $this->hasMany(VehicleImage::class);
    }

    /**
     * @return BelongsTo<Country, $this>
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_id');
    }
}
