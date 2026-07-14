<?php

namespace App\Http\Resources;

use App\Models\InspectionSlot;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin InspectionSlot
 */
class SlotResource extends JsonResource
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
            'inspection_date' => (string) $this->resource->inspection_date,
            'inspection_time' => (string) $this->resource->inspection_time,
            'status' => (string) $this->resource->status,
            'buyer' => (string) $this->resource->buyer?->full_name,

            'vehicle' => (object) [
                'previous_owner' => (string) $this->resource->vehicle->previous_owner,
                'make' => (string) $this->resource->vehicle->make,
                'model' => (string) $this->resource->vehicle->model,
                'year' => (string) $this->resource->vehicle->year,
                'variant' => (string) $this->resource->vehicle->variant,
                'body_type' => (string) $this->resource->vehicle->body_type,
                'vin' => (string) $this->resource->vehicle->vin,
            ],

            'location' => (object) [
                'name' => (string) $this->resource->location->name,
                'address' => (string) $this->resource->location->address,
                'city' => (string) $this->resource->location->city,
                'state' => (string) $this->resource->location->state,
                'note' => (string) $this->resource->location->note,
                'country' => (string) $this->resource->location->country?->name,
            ],

        ];
    }
}
