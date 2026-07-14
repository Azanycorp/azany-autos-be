<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
/**
 * @mixin \App\Models\InspectionLocation
 */
class InspectionLocationResource extends JsonResource
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
            'name' => (string) $this->resource->name,
            'address' => (string) $this->resource->address,
            'city' => (string) $this->resource->city,
            'state' => (string) $this->resource->state,
            'country' => (string) $this->resource->country?->name,
            'note' => (string) $this->resource->note,
            'is_default' => (bool) $this->resource->is_default,
        ];
    }
}
