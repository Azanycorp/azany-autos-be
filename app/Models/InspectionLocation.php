<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use Database\Factories\InspectionLocationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property string $address
 * @property string $city
 * @property string $state
 * @property string|null $note
 * @property int $country_id
 * @property bool $is_default
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property-read Country|null $country
 *
 * @method static \Database\Factories\InspectionLocationFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionLocation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionLocation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionLocation query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionLocation whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionLocation whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionLocation whereCountryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionLocation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionLocation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionLocation whereIsDefault($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionLocation whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionLocation whereNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionLocation whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionLocation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionLocation whereUserId($value)
 *
 * @mixin \Eloquent
 */
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
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<Country, $this>
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_id');
    }
}
