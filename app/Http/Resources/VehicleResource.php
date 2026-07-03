<?php

namespace App\Http\Resources;

use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Vehicle
 */
class VehicleResource extends JsonResource
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
            'make' => (string) $this->resource->make,
            'model' => (string) $this->resource->model,
            'year' => $this->resource->year,
            'listing_type' => (string) $this->resource->listing_type,
            'auction_days' => (int) $this->resource->auction_days,
            'auction_start_date' => $this->resource->auction_start_date,
            'auction_end_date' => $this->resource->auction_end_date,
            'country' => (string) $this->resource->country?->name,
            'city' => (string) $this->resource->city,
            'slug' => (string) $this->resource->slug,
            'status' => (string) $this->resource->status,
            'fuel_type' => (string) $this->resource->fuel_type,
            'transmission_type' => (string) $this->resource->transmission_type,
            'condition' => (string) $this->resource->condition,
            'kilometer_reading' => (string) $this->resource->kilometer_reading,
            'engine_capacity' => (string) $this->resource->engine_capacity,
            'previous_owner' => (string) $this->resource->previous_owner,
            'variant' => (string) $this->resource->variant,
            'body_type' => (string) $this->resource->body_type,
            'vin' => (string) $this->resource->vin,
            'accident_history' => (string) $this->resource->accident_history,
            'damage_history' => (string) $this->resource->damage_history,
            'service_history' => (string) $this->resource->service_history,
            'front_image' => (string) $this->resource->front_image,
            'back_image' => (string) $this->resource->back_image,
            'rear_image' => (string) $this->resource->rear_image,
            'passenger_side_image' => (string) $this->resource->passenger_side_image,
            'dashboard_image' => (string) $this->resource->dashboard_image,
            'video_link' => (string) $this->resource->video_link,
            'price' => (float) $this->resource->price,
            'description' => (string) $this->resource->description,
            'features' => $this->resource->features,
            'vehicle_images' => $this->resource->vehicleImages->map(function ($image) {
                return [
                    'id' => $image->id,
                    'image_path' => $image->image_path,
                ];
            }),
        ];
    }
}
