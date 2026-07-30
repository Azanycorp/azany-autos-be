<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'user_id',
    'vehicles',
    'fuel_types',
    'budget_min',
    'budget_max',
    'prefered_colors',
    'transmissions',
    'body_types',
])]

class BuyerPreference extends Model
{
    /** @use HasFactory<BuyerPreferenceFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'vehicles' => 'array',
            'fuel_types' => 'array',
            'prefered_colors' => 'array',
            'transmissions' => 'array',
            'body_types' => 'array',
        ];
    }
}
