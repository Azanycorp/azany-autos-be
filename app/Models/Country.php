<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use Database\Factories\CountryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string|null $sortname
 * @property string $name
 * @property string|null $phonecode
 * @property int $is_allowed
 * @property string|null $currency_code
 * @property string|null $flag
 * @property string|null $continent
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 *
 * @method static \Database\Factories\CountryFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country whereContinent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country whereCurrencyCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country whereFlag($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country whereIsAllowed($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country wherePhonecode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country whereSortname($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
#[Fillable(['sortname', 'name', 'phonecode', 'is_allowed', 'currency_code', 'flag', 'continent'])]

class Country extends Model
{
    /** @use HasFactory<CountryFactory> */
    use HasFactory;
}
