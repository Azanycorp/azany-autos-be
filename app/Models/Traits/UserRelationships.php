<?php

namespace App\Models\Traits;

use App\Enum\SubscriptionStatus;
use App\Models\Country;
use App\Models\FeatureTag;
use App\Models\InspectionLocation;
use App\Models\InspectionSlot;
use App\Models\Subscription;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @mixin Model
 */
trait UserRelationships
{
    /**
     * Get the country associated with the user.
     *
     * @return BelongsTo<Country, $this>
     *                                   *
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    /**
     * @return HasMany<Vehicle, $this>
     */
    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class);
    }

    /**
     * @return HasMany<FeatureTag, $this>
     */
    public function customTags(): HasMany
    {
        return $this->hasMany(FeatureTag::class);
    }

    /**
     * @return HasMany<InspectionLocation, $this>
     */
    public function inspectionLocations(): HasMany
    {
        return $this->hasMany(InspectionLocation::class);
    }

    /**
     * @return HasMany<InspectionSlot, $this>
     */
    public function inspectionSlots(): HasMany
    {
        return $this->hasMany(InspectionSlot::class, 'dealer_id', 'id');
    }

    /**
     * @return HasMany<Subscription, $this>
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * @return HasOne<Subscription, $this>
     */
    public function activeSubscription(): HasOne
    {
        return $this->hasOne(Subscription::class)
            ->where('status', SubscriptionStatus::ACTIVE)
            ->where(function ($query) {
                $query->whereNull('end_at')
                    ->orWhere('end_at', '>', now());
            })
            ->latestOfMany('starts_at');
    }
}
