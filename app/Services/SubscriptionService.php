<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\SubscriptionException;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SubscriptionService
{
    public function __construct(private readonly WalletService $walletService) {}

    /**
     * @return Collection<int, SubscriptionPlan>
     */
    public function index(): Collection
    {
        return SubscriptionPlan::with('features:id,feature,has_feature')->get();
    }

    public function show(int $id): ?SubscriptionPlan
    {
        return SubscriptionPlan::with('features:id,feature,has_feature')->find($id);
    }

    public function upgrade(int $userId, int $planId): Subscription
    {
        return DB::transaction(function () use ($userId, $planId) {
            $subscription = $this->getActiveSubscriptionOrFail($userId);
            $newPlan = SubscriptionPlan::findOrFail($planId);

            if ($newPlan->id === $subscription->subscription_plan_id) {
                throw new SubscriptionException('You are already on this plan.');
            }

            if ((float) $newPlan->price <= (float) $subscription->plan->price) {
                throw new SubscriptionException('Use the downgrade endpoint to move to a lower-priced plan.');
            }

            $proratedAmount = $this->calculateProratedAmount($subscription, $newPlan);

            if ($proratedAmount > 0) {
                $this->walletService->debit(
                    $subscription->user,
                    $proratedAmount,
                    "Upgrade to {$newPlan->name} plan (prorated)"
                );
            }

            $subscription->update([
                'subscription_plan_id' => $newPlan->id,
                'amount' => $newPlan->price,
                'next_subscription_plan_id' => null, // clear any pending downgrade
                'cancelled_at' => null, // upgrading un-cancels, matches most billing UX
            ]);

            return $subscription->fresh(['plan', 'nextPlan']);
        });
    }

    public function downgrade(int $userId, int $planId): Subscription
    {
        $subscription = $this->getActiveSubscriptionOrFail($userId);
        $newPlan = SubscriptionPlan::findOrFail($planId);

        if ($newPlan->id === $subscription->subscription_plan_id) {
            throw new SubscriptionException('You are already on this plan.');
        }

        if ((float) $newPlan->price >= (float) $subscription->plan->price) {
            throw new SubscriptionException('Use the upgrade endpoint to move to a higher-priced plan.');
        }

        // No charge now — applied by the renewal job when renews_at arrives.
        $subscription->update([
            'next_subscription_plan_id' => $newPlan->id,
        ]);

        return $subscription->fresh(['plan', 'nextPlan']);
    }

    public function cancel(int $userId): Subscription
    {
        $subscription = $this->getActiveSubscriptionOrFail($userId);

        if ($subscription->cancelled_at) {
            throw new SubscriptionException('Subscription is already scheduled for cancellation.');
        }

        // status stays 'active' — the job at renews_at moves it to 'cancelled'
        $subscription->update([
            'cancelled_at' => now(),
        ]);

        return $subscription->fresh(['plan', 'nextPlan']);
    }

    private function getActiveSubscriptionOrFail(int $userId): Subscription
    {
        $subscription = Subscription::with('plan')
            ->where('user_id', $userId)
            ->whereIn('status', ['active', 'trialing'])
            ->first();

        if (! $subscription) {
            throw new SubscriptionException('No active subscription found.');
        }

        return $subscription;
    }

    private function calculateProratedAmount(Subscription $subscription, SubscriptionPlan $newPlan): float
    {
        $anchor = $subscription->renews_at ?? $subscription->end_at;

        if (! $anchor) {
            // No renewal cycle to prorate against (e.g. lifetime/no expiry) — charge full price.
            return (float) $newPlan->price;
        }

        $cycleStart = $anchor->copy()->subMonth();
        $totalDays = max($cycleStart->diffInDays($anchor), 1);
        $daysRemaining = max(now()->diffInDays($anchor, false), 0);

        $unusedCredit = ((float) $subscription->plan->price / $totalDays) * $daysRemaining;
        $newPlanCost = ((float) $newPlan->price / $totalDays) * $daysRemaining;

        return round(max($newPlanCost - $unusedCredit, 0), 2);
    }
}
