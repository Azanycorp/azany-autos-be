<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'user_id',
    'slug',
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
}
