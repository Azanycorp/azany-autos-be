<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $subscription_plan_id
 * @property string $feature
 * @property int $has_feature
 * @property string|null $deleted_at
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property-read SubscriptionPlan|null $plan
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubscriptionPlanFeature newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubscriptionPlanFeature newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubscriptionPlanFeature query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubscriptionPlanFeature whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubscriptionPlanFeature whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubscriptionPlanFeature whereFeature($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubscriptionPlanFeature whereHasFeature($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubscriptionPlanFeature whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubscriptionPlanFeature whereSubscriptionPlanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubscriptionPlanFeature whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
#[Fillable(
    'subscription_plan_id',
    'feature',
    'has_feature'
)]
class SubscriptionPlanFeature extends Model
{
    /**
     * @return BelongsTo<SubscriptionPlan, $this>
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class);
    }
}
