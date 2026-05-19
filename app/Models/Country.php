<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['sortname', 'name', 'phonecode', 'is_allowed', 'currency_code', 'flag', 'continent'])]

class Country extends Model
{
}
