<?php

namespace App\Services;

use App\Enum\VehicleStatus;
use App\Http\Resources\VehicleResource;
use App\Traits\HttpResponses;
use Symfony\Component\HttpFoundation\JsonResponse;

class DealerService
{
    use HttpResponses;

    public function addVehicle($request): JsonResponse
    {
        $frontPath = uploadImage($request->file('front_image'), 'vehicles');
        $backPath = uploadImage($request->file('back_image'), 'vehicles');
        $rear_image = uploadImage($request->file('rear_image'), 'vehicles');
        $passenger_side_image = uploadImage($request->file('passenger_side_image'), 'vehicles');
        $dashboard_image = uploadImage($request->file('dashboard_image'), 'vehicles');
        $youtube_video = $request->hasFile('video_link') ? uploadImage($request->file('video_link'), 'vehicles') : null;

        $user = userAuth();
        $new_vehicle = $user->vehicles()->create([
            'make' => $request->make,
            'model' => $request->model,
            'year' => $request->year,
            'price' => $request->price,
            'slug' => str()->slug("{$request->make} {$request->model} {$request->year}").'-'.str()->random(6),
            'status' => VehicleStatus::PENDING->value,
            'listing_type' => $request->listing_type,
            'country_id' => $request->country_id,
            'city' => $request->city,
            'fuel_type' => $request->fuel_type,
            'transmission_type' => $request->transmission_type,
            'condition' => $request->condition,
            'kilometer_reading' => $request->kilometer_reading,
            'engine_capacity' => $request->engine_capacity,
            'previous_owner' => $request->previous_owner,
            'variant' => $request->variant,
            'body_type' => $request->body_type,
            'vin' => $request->vin,
            'accident_history' => $request->accident_history,
            'damage_history' => $request->damage_history,
            'service_history' => $request->service_history,
            'front_image' => $frontPath,
            'back_image' => $backPath,
            'rear_image' => $rear_image,
            'passenger_side_image' => $passenger_side_image,
            'dashboard_image' => $dashboard_image,
            'video_link' => $youtube_video,
            'description' => $request->description,
            'features' => $request->features,
        ]);

        if($new_vehicle) {
          uploadMultipleVehicleImages($request,'vehicle_images', 'vehicle_images', $new_vehicle);
        }

        return $this->successResponse(new VehicleResource($new_vehicle), 'Vehicle added successfully');
    }

    public function getVehicles($request): JsonResponse
    {
        $user = userAuth();
        $vehicles = $user->vehicles;

        return $this->successResponse(VehicleResource::collection($vehicles), 'Vehicles retrieved successfully');
    }

    public function getVehicle(int $id): JsonResponse
    {
        $user = userAuth();

        $vehicle = $user->vehicles->find($id);

        if (! $vehicle) {
            return $this->errorResponse(null, 'Vehicle not found', 404);
        }

        return $this->successResponse(new VehicleResource($vehicle), 'Vehicle retrieved successfully');
    }

    public function updateVehicle($request, int $id): JsonResponse
    {
        $user = userAuth();

        $vehicle = $user->vehicles->find($id);

        if (! $vehicle) {
            return $this->errorResponse(null, 'Vehicle not found', 404);
        }

        $frontPath = $request->hasFile('front_image') ? uploadImage($request->file('front_image'), 'vehicles') : $vehicle->front_image;
        $backPath = $request->hasFile('back_image') ? uploadImage($request->file('back_image'), 'vehicles') : $vehicle->back_image;
        $rear_image = $request->hasFile('rear_image') ? uploadImage($request->file('rear_image'), 'vehicles') : $vehicle->rear_image;
        $passenger_side_image = $request->hasFile('passenger_side_image') ? uploadImage($request->file('passenger_side_image'), 'vehicles') : $vehicle->passenger_side_image;
        $dashboard_image = $request->hasFile('dashboard_image') ? uploadImage($request->file('dashboard_image'), 'vehicles') : $vehicle->dashboard_image;
        $youtube_video = $request->hasFile('video_link') ? uploadImage($request->file('video_link'), 'vehicles') : $vehicle->video_link;

        $vehicle->update([
            'make' => $request->make,
            'model' => $request->model,
            'year' => $request->year,
            'price' => $request->price,
            'listing_type' => $request->listing_type,
            'country_id' => $request->country_id,
            'city' => $request->city,
            'fuel_type' => $request->fuel_type,
            'transmission_type' => $request->transmission_type,
            'condition' => $request->condition,
            'kilometer_reading' => $request->kilometer_reading,
            'engine_capacity' => $request->engine_capacity,
            'previous_owner' => $request->previous_owner,
            'variant' => $request->variant,
            'body_type' => $request->body_type,
            'vin' => $request->vin,
            'accident_history' => $request->accident_history,
            'damage_history' => $request->damage_history,
            'service_history' => $request->service_history,
            'front_image' => $frontPath,
            'back_image' => $backPath,
            'rear_image' => $rear_image,
            'passenger_side_image' => $passenger_side_image,
            'dashboard_image' => $dashboard_image,
            'video_link' => $youtube_video,
            'description' => $request->description,
            'features' => $request->features,
        ]);

        return $this->successResponse(new VehicleResource($vehicle), 'Vehicle updated successfully');
    }

    public function deleteVehicle(int $id): JsonResponse
    {
        $user = userAuth();

        $vehicle = $user->vehicles->find($id);

        if (! $vehicle) {
            return $this->errorResponse(null, 'Vehicle not found', 404);
        }

        $vehicle->vehicleImages()->delete();
        $vehicle->delete();

        return $this->successResponse(null, 'Vehicle deleted successfully');
    }
}



