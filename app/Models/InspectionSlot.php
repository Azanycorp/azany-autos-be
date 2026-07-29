<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $dealer_id
 * @property int|null $buyer_id
 * @property int $vehicle_id
 * @property int $location_id
 * @property string $inspection_date
 * @property string $inspection_time
 * @property string $status
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property-read User|null $buyer
 * @property-read User|null $dealer
 * @property-read InspectionLocation|null $location
 * @property-read Vehicle|null $vehicle
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionSlot newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionSlot newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionSlot query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionSlot whereBuyerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionSlot whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionSlot whereDealerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionSlot whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionSlot whereInspectionDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionSlot whereInspectionTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionSlot whereLocationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionSlot whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionSlot whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionSlot whereVehicleId($value)
 *
 * @mixin \Eloquent
 */
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
