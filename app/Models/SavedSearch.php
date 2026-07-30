<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable([
    'user_id',
    'name',
    'listing_type',
    'fuel_type',
    'transmission_type',
    'condition',
    'kilometer_reading',
    'make',
    'model',
    'min_year',
    'max_year',
    'min_price',
    'max_price',
    'country_id',
    'body_type',
    'is_notify'
])]

class SavedSearch extends Model
{
    //
}
