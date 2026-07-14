<?php

namespace App\Models;

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
    /**
     * @return BelongsTo<User, $this>
     */
    public function dealer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dealer_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    /**
     * @return BelongsTo<Vehicle, $this>
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
    }

     /**
     * @return BelongsTo<InspectionLocation, $this>
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(InspectionLocation::class, 'location_id');
    }
}
