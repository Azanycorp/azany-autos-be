<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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

class BuyerPreference extends Model {

protected function casts(): array
    {
        return [
            'vehicle_ids' => 'array',
            'body_types' => 'array',
            'fuel_types' => 'array',
            'transmissions' => 'array',
            'preferred_colors' => 'array',
        ];
    }

       /**
     * @return BelongsTo<User, $this>
     */
    public function customTags(): BelongsTo
    {
        return $this->BelongsTo(User::class);
    }

}
