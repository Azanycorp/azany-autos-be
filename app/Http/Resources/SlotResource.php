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
                'previous_owner' => (string) optional($this->resource->vehicle)->previous_owner,
                'make' => (string) optional($this->resource->vehicle)->make,
                'model' => (string) optional($this->resource->vehicle)->model,
                'year' => (string) optional($this->resource->vehicle)->year,
                'variant' => (string) optional($this->resource->vehicle)->variant,
                'body_type' => (string) optional($this->resource->vehicle)->body_type,
                'vin' => (string) optional($this->resource->vehicle)->vin,
            ],

            'location' => (object) [
                'name' => (string) optional($this->resource->location)->name,
                'address' => (string) optional($this->resource->location)->address,
                'city' => (string) optional($this->resource->location)->city,
                'state' => (string) optional($this->resource->location)->state,
                'note' => (string) optional($this->resource->location)->note,
                'country' => (string) optional($this->resource->location)->country?->name,
            ],

        ];
    }
}
