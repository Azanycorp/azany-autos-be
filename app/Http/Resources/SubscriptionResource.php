<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\JsonApi\JsonApiResource;

class SubscriptionResource extends JsonApiResource
{
    /**
     * Get the resource's attributes.
     *
     * @return array<string, mixed>
     */
    public function toAttributes(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'status' => $this->resource->status,
            'auto_renew' => $this->resource->auto_renew,
            'amount' => $this->resource->amount,
            'gateway' => $this->resource->gateway,
            'starts_at' => $this->resource->starts_at?->toDateString(),
            'renews_at' => $this->resource->renews_at?->toDateString(),
            'end_at' => $this->resource->end_at?->toDateString(),
            'cancelled_at' => $this->resource->cancelled_at?->toDateString(),
            'plan' => new SubscriptionPlanResource($this->whenLoaded('plan')),
            'pending_downgrade_plan' => new SubscriptionPlanResource($this->whenLoaded('nextPlan')),
        ];
    }
}
