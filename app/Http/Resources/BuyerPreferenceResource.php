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
            'id' => (int) $this->resource->id,
            'first_name' => (string) $this->resource->first_name,
    'vehicle_ids',
    'fuel_types',
    'budget_min',
    'budget_max',
    'prefered_colors',
    'transmissions',
    'body_types',

     'vehicles' => $this->resource->vehicleImages->map(function ($image) {
                return [
                    'id' => $image->id,
                    'image_path' => $image->image_path,
                ];
            }),
        ];
    }
}
