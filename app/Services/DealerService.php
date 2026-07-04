<?php

namespace App\Services;

use App\Enum\VehicleStatus;
use App\Http\Requests\V1\TagRequest;
use App\Http\Requests\V1\UpdateVehicleRequest;
use App\Http\Requests\V1\VehicleRequest;
use App\Http\Resources\TagResource;
use App\Http\Resources\VehicleResource;
use App\Models\FeatureTag;
use App\Models\Vehicle;
use App\Models\VehicleImage;
use App\Traits\HttpResponses;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DealerService
{
    use HttpResponses;

    public function addVehicle(VehicleRequest $request): JsonResponse
    {
        $frontPath = uploadImage($request->file('front_image'), 'vehicles');
        $backPath = uploadImage($request->file('back_image'), 'vehicles');
        $rear_image = uploadImage($request->file('rear_image'), 'vehicles');
        $passenger_side_image = uploadImage($request->file('passenger_side_image'), 'vehicles');
        $dashboard_image = uploadImage($request->file('dashboard_image'), 'vehicles');
        $youtube_video = $request->hasFile('video_link') ? uploadImage($request->file('video_link'), 'vehicles') : null;

        $user = userAuth();

        try {
            $new_vehicle = DB::transaction(function () use ($request, $user, $frontPath, $backPath, $rear_image, $passenger_side_image, $dashboard_image, $youtube_video) {

                $vehicle = $user->vehicles()->create([
                    'make' => $request->make,
                    'model' => $request->model,
                    'year' => $request->year,
                    'reserved_price' => $request->reserved_price,
                    'price' => $request->price,
                    'slug' => str()->slug("{$request->make} {$request->model} {$request->year}").'-'.str()->random(6),
                    'status' => VehicleStatus::PENDING->value,
                    'listing_type' => $request->listing_type,
                    'country_id' => $request->country_id,
                    'auction_days' => $request->auction_days,
                    'auction_start_date' => now(),
                    'auction_end_date' => calculateAuctionDuration($request->auction_days),
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

                /** @var Vehicle $vehicle */
                uploadMultipleVehicleImages($request, 'vehicle_images', 'vehicle_images', $vehicle);

                return $vehicle;
            });

            return $this->successResponse(new VehicleResource($new_vehicle), 'Vehicle added successfully');

        } catch (Exception $e) {

            return $this->errorResponse(null, 'Something went wrong while saving the vehicle listing.', 403);
        }
    }

    public function getVehicles(Request $request): JsonResponse
    {
        $type = $request->query('type');
        $sort = $request->query('sort');

        $user = userAuth();

        $vehicles = Vehicle::with(['vehicleImages'])
            ->where('user_id', $user->id)
            ->when($type, fn ($q) => $q->where('status', $type))
            ->when($sort, function ($q) use ($sort) {
                match ($sort) {
                    'price_asc' => $q->orderBy('price', 'asc'),
                    'price_desc' => $q->orderBy('price', 'desc'),
                    'newest' => $q->orderBy('condition', 'asc'),
                    'oldest' => $q->orderBy('condition', 'desc'),
                    default => $q->latest(),
                };
            }, fn ($q) => $q->latest())
            ->paginate(intval($request->query('per_page') ?? 25));

        return $this->withPagination(VehicleResource::collection($vehicles), 'Vehicles retrieved successfully');
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

    public function updateVehicle(UpdateVehicleRequest $request, int $id): JsonResponse
    {
        $user = userAuth();

        $vehicle = Vehicle::where('user_id', $user->id)->where('id', $id)->first();

        if (! $vehicle) {
            return $this->errorResponse(null, 'Vehicle not found', 404);
        }

        if ($vehicle->vin !== $request->vin) {
            $existingVehicle = Vehicle::where('vin', $request->vin)->first();
            if ($existingVehicle) {
                return $this->errorResponse(null, 'VIN already exists for another vehicle', 422);
            }
        }

        $frontPath = $request->hasFile('front_image') ? uploadImage($request->file('front_image'), 'vehicles') : $vehicle->front_image;
        $backPath = $request->hasFile('back_image') ? uploadImage($request->file('back_image'), 'vehicles') : $vehicle->back_image;
        $rear_image = $request->hasFile('rear_image') ? uploadImage($request->file('rear_image'), 'vehicles') : $vehicle->rear_image;
        $passenger_side_image = $request->hasFile('passenger_side_image') ? uploadImage($request->file('passenger_side_image'), 'vehicles') : $vehicle->passenger_side_image;
        $dashboard_image = $request->hasFile('dashboard_image') ? uploadImage($request->file('dashboard_image'), 'vehicles') : $vehicle->dashboard_image;
        $youtube_video = $request->hasFile('video_link') ? uploadImage($request->file('video_link'), 'vehicles') : $vehicle->video_link;

        $vehicle->update([
            'make' => $request->make ?? $vehicle->make,
            'model' => $request->model ?? $vehicle->model,
            'year' => $request->year ?? $vehicle->year,
            'price' => $request->price ?? $vehicle->price,
            'listing_type' => $request->listing_type ?? $vehicle->listing_type,
            'country_id' => $request->country_id ?? $vehicle->country_id,
            'city' => $request->city ?? $vehicle->city,
            'fuel_type' => $request->fuel_type ?? $vehicle->fuel_type,
            'transmission_type' => $request->transmission_type ?? $vehicle->transmission_type,
            'condition' => $request->condition ?? $vehicle->condition,
            'kilometer_reading' => $request->kilometer_reading ?? $vehicle->kilometer_reading,
            'engine_capacity' => $request->engine_capacity ?? $vehicle->engine_capacity,
            'previous_owner' => $request->previous_owner ?? $vehicle->previous_owner,
            'variant' => $request->variant ?? $vehicle->variant,
            'body_type' => $request->body_type ?? $vehicle->body_type,
            'vin' => $request->vin ?? $vehicle->vin,
            'accident_history' => $request->accident_history ?? $vehicle->accident_history,
            'damage_history' => $request->damage_history ?? $vehicle->damage_history,
            'service_history' => $request->service_history ?? $vehicle->service_history,
            'front_image' => $frontPath,
            'back_image' => $backPath,
            'rear_image' => $rear_image,
            'passenger_side_image' => $passenger_side_image,
            'dashboard_image' => $dashboard_image,
            'video_link' => $youtube_video,
            'description' => $request->description ?? $vehicle->description,
            'features' => $request->features ?? $vehicle->features,
        ]);

        if ($request->hasFile('vehicle_images')) {
            uploadMultipleVehicleImages($request, 'vehicle_images', 'vehicle_images', $vehicle);
        }

        return $this->successResponse(new VehicleResource($vehicle), 'Vehicle updated successfully');
    }

    public function updateVehicleStatus(Request $request, int $id): JsonResponse
    {
        $user = userAuth();

        $vehicle = $user->vehicles->find($id);

        if (! $vehicle) {
            return $this->errorResponse(null, 'Vehicle not found', 404);
        }

        $vehicle->update([
            'status' => $request->status,
        ]);

        return $this->successResponse(null, 'Vehicle status updated successfully');
    }

    public function deleteVehicle(int $id): JsonResponse
    {
        $user = userAuth();

        $vehicle = Vehicle::where('user_id', $user->id)->where('id', $id)->first();

        if (! $vehicle) {
            return $this->errorResponse(null, 'Vehicle not found', 404);
        }

        $vehicle->vehicleImages()->delete();
        $vehicle->delete();

        return $this->successResponse(null, 'Vehicle deleted successfully');
    }

    public function deleteVehicleImage(int $vehicle_id, int $id): JsonResponse
    {
        $vehicle_image = VehicleImage::where('vehicle_id', $vehicle_id)->where('id', $id)->first();

        if (! $vehicle_image) {
            return $this->errorResponse(null, 'Vehicle image not found', 404);
        }

        $vehicle_image->delete();

        return $this->successResponse(null, 'Image deleted successfully');
    }

    public function addCustomTag(TagRequest $request): JsonResponse
    {
        $user = userAuth();
        $tag = $user->customTags()->create([
            'name' => $request->name,
        ]);

        return $this->successResponse(new TagResource($tag), 'Custom tag added successfully');
    }

    public function getTags(): JsonResponse
    {
        $user = userAuth();

        $tags = FeatureTag::where('user_id', $user->id)->latest()->get();

        return $this->successResponse(TagResource::collection($tags), 'Tags retrieved successfully');
    }

    public function getTag(int $id): JsonResponse
    {
        $user = userAuth();

        $tag = FeatureTag::where('user_id', $user->id)->where('id', $id)->first();

        if (! $tag) {
            return $this->errorResponse(null, 'Tag not found', 404);
        }

        return $this->successResponse(new TagResource($tag), 'Tag retrieved successfully');
    }

    public function updateTag(Request $request, int $id): JsonResponse
    {
        $user = userAuth();

        $tag = FeatureTag::where('user_id', $user->id)->where('id', $id)->first();

        if (! $tag) {
            return $this->errorResponse(null, 'Tag not found', 404);
        }

        if ($tag->name !== $request->name) {
            $existingTag = FeatureTag::where('user_id', $user->id)->where('name', $request->name)->first();
            if ($existingTag) {
                return $this->errorResponse(null, 'Tag name already exists', 422);
            }
        }

        $tag->update([
            'name' => $request->name ?? $tag->name,
        ]);

        return $this->successResponse(new TagResource($tag), 'Tag updated successfully');
    }

    public function deleteTag(int $id): JsonResponse
    {
        $user = userAuth();

        $tag = FeatureTag::where('user_id', $user->id)->where('id', $id)->first();

        if (! $tag) {
            return $this->errorResponse(null, 'Tag not found', 404);
        }

        $tag->delete();

        return $this->successResponse(null, 'Tag deleted successfully');
    }
}
