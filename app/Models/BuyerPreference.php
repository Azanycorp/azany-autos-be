<?php

namespace App\Models;

use Database\Factories\BuyerPreferenceFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'user_id',
    'vehicle_ids',
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
            'vehicle_ids' => 'array',
            'fuel_types' => 'array',
            'prefered_colors' => 'array',
            'transmissions' => 'array',
            'body_types' => 'array',
        ];
    }

    /**
     * @return Attribute<Collection<int, Vehicle>, null>
     */
    protected function vehicles(): Attribute
    {
        $ids = $this->vehicle_ids ?? [];

        if (is_string($ids)) {
            $ids = json_decode($ids, true);
        }

        return Attribute::make(get: function () {
            $ids = $this->vehicle_ids ?? [];
            if (is_string($ids)) {
                $ids = json_decode($ids, true);
            }

            return Vehicle::whereIn('id', $ids)->get();
        });
    }
}
