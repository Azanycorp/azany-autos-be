<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin User
 */
class SavedSearchResource extends JsonResource
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
            'listing_type' => (string) $this->resource->listing_type,
            'fuel_type' => (string) $this->resource->fuel_type,
            'transmission_type' => (string) $this->resource->transmission_type,
            'condition' => (string) $this->resource->condition,
            'kilometer_reading' => (string) $this->resource->kilometer_reading,
            'make' => (string) $this->resource->make,
            'model' => (string) $this->resource->model,
            'min_year' => (string) $this->resource->min_year,
            'max_year' => (string) $this->resource->max_year,
            'min_price' => (float) $this->resource->min_price,
            'max_price' => (float) $this->resource->max_price,
            'country' => (string) $this->resource->country?->name,
            'body_type' => (string) $this->resource->body_type,
            'is_notify' => (bool) $this->resource->is_notify,
        ];
    }
}
