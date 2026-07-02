<?php

namespace App\Services;

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
       'slug' => str()->slug("{$request->make} {$request->model} {$request->year}") . '-' . str()->random(6),
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

        return $this->successResponse($new_vehicle, 'Vehicle added successfully');
    }
}
