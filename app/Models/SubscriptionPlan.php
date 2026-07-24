<?php

namespace App\Models;

use Database\Factories\SubscriptionPlanFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(
    'name',
    'slug',
    'price',
    'billing_cycle',
    'is_active'
)]
class SubscriptionPlan extends Model
{
    /** @use HasFactory<SubscriptionPlanFactory> */
    use HasFactory;

    /**
     * @return HasMany<SubscriptionPlanFeature, $this>
     */
    public function features(): HasMany
    {
        return $this->hasMany(SubscriptionPlanFeature::class);
    }
}
