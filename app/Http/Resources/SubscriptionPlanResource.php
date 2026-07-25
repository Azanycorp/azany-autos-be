<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\JsonApi\JsonApiResource;

class SubscriptionPlanResource extends JsonApiResource
{
    /**
     * The resource's attributes.
     *
     * @var array<int, string>
     */
    public $attributes = [
        'id',
        'name',
        'slug',
        'price',
        'billing_cycle',
    ];

    /**
     * The resource's relationships.
     *
     * @var array<int, string>
     */
    public $relationships = [
        'features',
    ];
}
