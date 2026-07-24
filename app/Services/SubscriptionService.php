<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\SubscriptionPlan;
use Illuminate\Support\Collection;

class SubscriptionService
{
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
}
