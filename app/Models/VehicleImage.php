<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'vehicle_id',
    'image_path',
])]
class VehicleImage extends Model
{
    //
}
