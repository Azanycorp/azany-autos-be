<?php

declare(strict_types=1);

use App\Models\SubscriptionPlan;
use App\Services\SubscriptionService;

it('gets all subscriptions', function () {
    SubscriptionPlan::factory()->create();

    expect(resolve(SubscriptionService::class)->index())->toHaveCount(1);
});

it('get a subscription by id', function () {
    $subscription = SubscriptionPlan::factory()->create();

    expect(resolve(SubscriptionService::class)
        ->show($subscription->id))
        ->toBeInstanceOf(SubscriptionPlan::class);
});
