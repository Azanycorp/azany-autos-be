<?php

namespace App\Models;

use App\Enum\SubscriptionStatus;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Database\Factories\SubscriptionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * @property int $user_id,
 * @property int $subscription_plan_id,
 * @property int|null $next_subscription_plan_id,
 * @property float $amount,
 * @property Carbon $starts_at,
 * @property Carbon $end_at,
 * @property int $id
 * @property int $user_id
 * @property int $subscription_plan_id
 * @property numeric $amount
 * @property CarbonImmutable $starts_at
 * @property CarbonImmutable|null $end_at
 * @property CarbonImmutable|null $trial_ends_at Trial expiration date, if applicable
 * @property CarbonImmutable|null $cancelled_at
 * @property CarbonImmutable|null $renews_at Next scheduled renewal date
 * @property string|null $gateway Payment gateway used for this subscription (e.g. Paystack, Stripe)
 * @property SubscriptionStatus $status Subscription status (active, trialing, cancelled, expired, pending)
 * @property array<array-key, mixed>|null $metadata Additional subscription metadata from the payment provider
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property bool $auto_renew
 * @property-read SubscriptionPlan|null $nextPlan
 * @property-read Collection<int, Payment> $payments
 * @property-read int|null $payments_count
 * @property-read SubscriptionPlan $plan
 * @property-read User|null $user
 *
 * @method static \Database\Factories\SubscriptionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereCancelledAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereEndAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereGateway($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereMetadata($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereRenewsAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereStartsAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereSubscriptionPlanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereTrialEndsAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereUserId($value)
 *
 * @mixin \Eloquent
 */
#[Fillable(
    'user_id',
    'subscription_plan_id',
    'next_subscription_plan_id',
    'amount',
    'starts_at',
    'end_at',
    'trial_ends_at',
    'cancelled_at',
    'renews_at',
    'gateway',
    'status',
    'metadata',
)]
class Subscription extends Model
{
    /** @use HasFactory<SubscriptionFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'starts_at' => 'datetime',
            'end_at' => 'datetime',
            'trial_ends_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'renews_at' => 'datetime',
            'metadata' => 'array',
            'status' => SubscriptionStatus::class,
        ];
    }

    /**
     * @return MorphMany<Payment, $this>
     */
    public function payments(): MorphMany
    {
        return $this->morphMany(Payment::class, 'payable');
    }

    /**
     * @return BelongsTo<SubscriptionPlan, $this>
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class, 'subscription_plan_id');
    }

    /**
     * @return BelongsTo<SubscriptionPlan, $this>
     */
    public function nextPlan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class, 'next_subscription_plan_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return Attribute<non-falsy-string, never>
     */
    protected function autoRenew(): Attribute
    {
        return Attribute::make(
            get: fn () => is_null($this->cancelled_at),
            set: fn (bool $value) => [
                'cancelled_at' => $value ? null : now(),
            ],
        );
    }
}
