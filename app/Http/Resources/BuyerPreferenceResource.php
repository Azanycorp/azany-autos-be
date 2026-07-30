<?php

namespace App\Http\Resources;

use App\Models\BuyerPreference;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin BuyerPreference
 */
class BuyerPreferenceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'fuel_types' => $this->resource->fuel_types,
            'budget_min' => (float) $this->resource->budget_min,
            'budget_max' => (float) $this->resource->budget_max,
            'prefered_colors' => $this->resource->prefered_colors,
            'transmissions' => $this->resource->transmissions,
            'body_types' => $this->resource->body_types,
            'vehicles' => $this->resource->vehicles,
        ];
    }
}
