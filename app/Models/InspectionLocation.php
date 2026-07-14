<?php

namespace App\Models;

use Database\Factories\InspectionLocationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id',
    'name',
    'address',
    'city',
    'state',
    'note',
    'country_id',
    'is_default',
])]

class InspectionLocation extends Model
{
    /** @use HasFactory<InspectionLocationFactory> */
    use HasFactory;

    /**
     * @return BelongsTo<Country, $this>
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_id');
    }
}
