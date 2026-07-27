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
            'fuel_types' => (string) $this->resource->fuel_types,
            'budget_min' => (string) $this->resource->budget_min,
            'budget_max' => (string) $this->resource->budget_max,
            'prefered_colors' => (string) $this->resource->prefered_colors,
            'transmissions' => (string) $this->resource->transmissions,
            'body_types' => (string) $this->resource->body_types,
            'vehicles' => $this->resource->vehicleImages->map(function ($vehicle) {
                return [
                    'make' => $vehicle->make,
                ];
            }),
        ];
    }
}
