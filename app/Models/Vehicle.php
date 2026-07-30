<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use Database\Factories\VehicleFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $user_id
 * @property string $slug
 * @property string $listing_type
 * @property int $auction_days
 * @property string|null $auction_start_date
 * @property string|null $auction_end_date
 * @property string $status
 * @property int $country_id
 * @property string $city
 * @property string $fuel_type
 * @property string $transmission_type
 * @property string $condition
 * @property string $kilometer_reading
 * @property string $engine_capacity
 * @property string|null $previous_owner
 * @property string $make
 * @property string $model
 * @property string $year
 * @property string|null $variant
 * @property string $body_type
 * @property string $vin
 * @property string $accident_history
 * @property string $damage_history
 * @property string|null $service_history
 * @property string $front_image
 * @property string $back_image
 * @property string $rear_image
 * @property string $passenger_side_image
 * @property string $dashboard_image
 * @property string|null $video_link
 * @property numeric $reserved_price
 * @property numeric $price
 * @property string $description
 * @property array<array-key, mixed> $features
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property-read Country|null $country
 * @property-read Collection<int, VehicleImage> $vehicleImages
 * @property-read int|null $vehicle_images_count
 *
 * @method static \Database\Factories\VehicleFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereAccidentHistory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereAuctionDays($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereAuctionEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereAuctionStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereBackImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereBodyType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereCondition($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereCountryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereDamageHistory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereDashboardImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereEngineCapacity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereFeatures($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereFrontImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereFuelType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereKilometerReading($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereListingType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereMake($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereModel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle wherePassengerSideImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle wherePreviousOwner($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereRearImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereReservedPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereServiceHistory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereTransmissionType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereVariant($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereVideoLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereVin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereYear($value)
 *
 * @mixin \Eloquent
 */
#[Fillable([
    'user_id',
    'slug',
    'status',
    'listing_type',
    'auction_days',
    'auction_start_date',
    'auction_end_date',
    'country_id',
    'city',
    'fuel_type',
    'transmission_type',
    'condition',
    'kilometer_reading',
    'engine_capacity',
    'previous_owner',
    'make',
    'model',
    'year',
    'variant',
    'body_type',
    'vin',
    'accident_history',
    'damage_history',
    'service_history',
    'front_image',
    'back_image',
    'rear_image',
    'passenger_side_image',
    'dashboard_image',
    'video_link',
    'reserved_price',
    'price',
    'description',
    'features',
])]

class Vehicle extends Model
{
    /** @use HasFactory<VehicleFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'features' => 'array',
        ];
    }

    /**
     * @return HasMany<VehicleImage, $this>
     */
    public function vehicleImages(): HasMany
    {
        return $this->hasMany(VehicleImage::class);
    }

    /**
     * @return BelongsTo<Country, $this>
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_id');
    }
}
