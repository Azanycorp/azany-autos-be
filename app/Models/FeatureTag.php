<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[fillable([
    'user_id',
    'name',
])]
class FeatureTag extends Model
{
    //
}
