<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $vehicle_id
 * @property string $image_path
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VehicleImage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VehicleImage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VehicleImage query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VehicleImage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VehicleImage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VehicleImage whereImagePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VehicleImage whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VehicleImage whereVehicleId($value)
 *
 * @mixin \Eloquent
 */
#[Fillable([
    'vehicle_id',
    'image_path',
])]
class VehicleImage extends Model
{
    //
}
