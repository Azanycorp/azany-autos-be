<?php

namespace App\Models;

use App\Models\InspectionLocation;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'dealer_id',
    'buyer_id',
    'vehicle_id',
    'location_id',
    'inspection_date',
    'inspection_time',
    'status',
])]

class InspectionSlot extends Model
{
    public function dealer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dealer_id');
    }

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(InspectionLocation::class, 'location_id');
    }
}
