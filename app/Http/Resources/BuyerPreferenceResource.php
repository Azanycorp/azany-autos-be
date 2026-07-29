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
            'fuel_types' => $this->fuel_types,
            'budget_min' => (float) $this->budget_min,
            'budget_max' => (float) $this->budget_max,
            'prefered_colors' => $this->prefered_colors,
            'transmissions' => $this->transmissions,
            'body_types' => $this->body_types,
            'vehicles' => $this->resource->vehicles->map(function ($vehicle) {
                return [
                    'make' => $vehicle->make,
                    'model' => $vehicle->model,
                ];
            }),
        ];
    }
}
