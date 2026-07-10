<?php

namespace App\Services;

use App\Enum\VehicleStatus;
use App\Http\Requests\V1\LocationRequest;
use App\Http\Requests\V1\SlotRequest;
use App\Http\Requests\V1\TagRequest;
use App\Http\Requests\V1\UpdateVehicleRequest;
use App\Http\Requests\V1\VehicleRequest;
use App\Http\Resources\InspectionLocationResource;
use App\Http\Resources\SlotResource;
use App\Http\Resources\TagResource;
use App\Http\Resources\VehicleResource;
use App\Models\FeatureTag;
use App\Models\InspectionLocation;
use App\Models\InspectionSlot;
use App\Models\User;
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
        if ($request->reserved_price > $request->price) {
            return $this->errorResponse(null, 'Reserved price cannot be higher than the actual price.', 400);
        }
        $user = User::where('id', $request->user_id)->first();

        if (! $user) {
            return $this->errorResponse(null, 'User not found', 404);
        }
        $frontPath = uploadImage($request->file('front_image'), 'vehicles');
        $backPath = uploadImage($request->file('back_image'), 'vehicles');
        $rear_image = uploadImage($request->file('rear_image'), 'vehicles');
        $passenger_side_image = uploadImage($request->file('passenger_side_image'), 'vehicles');
        $dashboard_image = uploadImage($request->file('dashboard_image'), 'vehicles');
        $youtube_video = $request->hasFile('video_link') ? uploadImage($request->file('video_link'), 'vehicles') : null;

        try {
            return DB::transaction(function () use ($request, $user, $frontPath, $backPath, $rear_image, $passenger_side_image, $dashboard_image, $youtube_video) {

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
                uploadMultipleImages($request, 'vehicle_images', 'vehicle_images', $vehicle, 'vehicleImages', 'image_path');

                return $this->successResponse(new VehicleResource($vehicle), 'Vehicle added successfully');
            });

        } catch (Exception $e) {

            return $this->errorResponse(null, 'Something went wrong while saving the vehicle listing.', 403);
        }
    }

    public function getVehicles(Request $request): JsonResponse
    {
        $type = $request->query('type');
        $sort = $request->query('sort');

        $user = $request->user();

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

    public function getVehicle(Request $request, int $id): JsonResponse
    {
        $user = $request->user();

        $vehicle = $user->vehicles->find($id);

        if (! $vehicle) {
            return $this->errorResponse(null, 'Vehicle not found', 404);
        }

        return $this->successResponse(new VehicleResource($vehicle), 'Vehicle retrieved successfully');
    }

    public function updateVehicle(UpdateVehicleRequest $request, int $id): JsonResponse
    {
        $user = $request->user();

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
            uploadMultipleImages($request, 'vehicle_images', 'vehicle_images', $vehicle, 'vehicleImages', 'image_path');
        }

        return $this->successResponse(new VehicleResource($vehicle), 'Vehicle updated successfully');
    }

    public function updateVehicleStatus(Request $request, int $id): JsonResponse
    {
        $user = $request->user();

        $vehicle = $user->vehicles->find($id);

        if (! $vehicle) {
            return $this->errorResponse(null, 'Vehicle not found', 404);
        }

        $vehicle->update([
            'status' => $request->status,
        ]);

        return $this->successResponse(null, 'Vehicle status updated successfully');
    }

    public function deleteVehicle(Request $request, int $id): JsonResponse
    {
        $user = $request->user();

        $vehicle = Vehicle::where('user_id', $user->id)->where('id', $id)->first();

        if (! $vehicle) {
            return $this->errorResponse(null, 'Vehicle not found', 404);
        }

        $vehicle->vehicleImages()->delete();
        $vehicle->delete();

        return $this->successResponse(null, 'Vehicle deleted successfully');
    }

    public function deleteVehicleImage(Request $request, int $id): JsonResponse
    {
        $vehicleImage = VehicleImage::where('vehicle_id', $request->vehicle_id)
            ->where('id', $id)
            ->first();

        if (! $vehicleImage) {
            return $this->errorResponse(null, 'Vehicle image not found', 404);
        }

        $vehicleImage->delete();

        return $this->successResponse(null, 'Image deleted successfully');
    }

    public function addCustomTag(TagRequest $request, User $user): JsonResponse
    {
        $tag = $user->customTags()->create([
            'name' => $request->name,
        ]);

        return $this->successResponse(new TagResource($tag), 'Custom tag added successfully');
    }

    public function getTags(int $userId): JsonResponse
    {
        $user = User::where('id', $userId)->first();

        if (! $user) {
            return $this->errorResponse(null, 'User not found', 404);
        }

        $tags = FeatureTag::where('user_id', $user->id)->latest()->get();

        return $this->successResponse(TagResource::collection($tags), 'Tags retrieved successfully');
    }

    public function getTag(int $id, User $user): JsonResponse
    {
        $tag = FeatureTag::where('user_id', $user->id)
            ->where('id', $id)
            ->first();

        if (! $tag) {
            return $this->errorResponse(null, 'Tag not found', 404);
        }

        return $this->successResponse(new TagResource($tag), 'Tag retrieved successfully');
    }

    public function updateTag(Request $request, int $id, User $user): JsonResponse
    {
        $tag = FeatureTag::where('user_id', $user->id)->where('id', $id)->first();

        if (! $tag instanceof FeatureTag) {
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

    public function deleteTag(int $id, User $user): JsonResponse
    {
        $tag = FeatureTag::where('user_id', $user->id)->where('id', $id)->first();

        if (! $tag instanceof FeatureTag) {
            return $this->errorResponse(null, 'Tag not found', 404);
        }

        $tag->delete();

        return $this->successResponse(null, 'Tag deleted successfully');
    }

    // Inspection Location
    public function addNewLocation(LocationRequest $request, User $user): JsonResponse
    {
        $location = $user->inspectionLocations()->create([
            'name' => $request->name,
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state,
            'note' => $request->note,
            'country_id' => $request->country_id,
        ]);

        return $this->successResponse(new InspectionLocationResource($location), 'Location added successfully');
    }

    public function getAllLocations(int $userId): JsonResponse
    {
        $user = User::where('id', $userId)->first();

        if (! $user) {
            return $this->errorResponse(null, 'User not found', 404);
        }

        $locations = InspectionLocation::where('user_id', $user->id)->latest()->get();

        return $this->successResponse(InspectionLocationResource::collection($locations), 'Locations retrieved successfully');
    }

    public function getLocation(int $id, User $user): JsonResponse
    {
        $location = InspectionLocation::where('user_id', $user->id)
            ->where('id', $id)
            ->first();

        if (! $location) {
            return $this->errorResponse(null, 'Location not found', 404);
        }

        return $this->successResponse(new InspectionLocationResource($location), 'Location retrieved successfully');
    }

    public function makeLocationDefault(int $id, User $user): JsonResponse
    {
        $location = InspectionLocation::where('user_id', $user->id)->where('id', $id)->first();

        if (! $location instanceof InspectionLocation) {
            return $this->errorResponse(null, 'Location not found', 404);
        }

        DB::transaction(function () use ($user, $location) {
            InspectionLocation::where('user_id', $user->id)
                ->where('is_default', true)
                ->update(['is_default' => false]);

            $location->is_default = true;
            $location->save();
        });

        return $this->successResponse(null, 'Location marked as default successfully');
    }

    public function updateLocation(Request $request, int $id, User $user): JsonResponse
    {
        $location = InspectionLocation::where('user_id', $user->id)->where('id', $id)->first();

        if (! $location instanceof InspectionLocation) {
            return $this->errorResponse(null, 'Location not found', 404);
        }

        if ($location->name !== $request->name) {
            $existingTag = InspectionLocation::where('user_id', $user->id)->where('name', $request->name)->first();
            if ($existingTag) {
                return $this->errorResponse(null, 'Location name already exists', 422);
            }
        }

        $location->update([
            'name' => $request->name ?? $location->name,
            'address' => $request->address ?? $location->address,
            'city' => $request->city ?? $location->city,
            'state' => $request->state ?? $location->state,
            'country_id' => $request->country_id ?? $location->country_id,
            'note' => $request->note ?? $location->note,
        ]);

        return $this->successResponse(new InspectionLocationResource($location), 'Location updated successfully');
    }

    public function deleteLocation(int $id, User $user): JsonResponse
    {
        $location = InspectionLocation::where('user_id', $user->id)->where('id', $id)->first();

        if (! $location instanceof InspectionLocation) {
            return $this->errorResponse(null, 'Location not found', 404);
        }

        if ($location->is_default == true) {
            return $this->errorResponse(null, 'Default Location can not be deleted', 403);
        }

        $location->delete();

        return $this->successResponse(null, 'Location deleted successfully');
    }

    // Inspection Slots
    public function addNewSlot(SlotRequest $request, User $user): JsonResponse
    {
        $slot = $user->inspectionSlots()->create([
            'vehicle_id' => $request->vehicle_id,
            'location_id' => $request->location_id,
            'inspection_date' => $request->inspection_date,
            'inspection_time' => $request->inspection_time,
        ]);

        return $this->successResponse(new SlotResource($slot), 'New Slot added successfully');
    }

    public function getAllSlots(int $userId): JsonResponse
    {
        $user = User::where('id', $userId)->first();

        if (! $user) {
            return $this->errorResponse(null, 'User not found', 404);
        }

        $slots = InspectionSlot::with(['location', 'vehicle'])->where('dealer_id', $user->id)->latest()->get();

        return $this->successResponse(SlotResource::collection($slots), 'Inspection slots retrieved successfully');
    }

    public function getSlot(int $id, User $user): JsonResponse
    {
        $slot = InspectionSlot::where('dealer_id', $user->id)
            ->where('id', $id)
            ->first();

        if (! $slot) {
            return $this->errorResponse(null, 'Slot not found', 404);
        }

        return $this->successResponse(new SlotResource($slot), 'Slot retrieved successfully');
    }

    public function updateSlotStatus(Request $request, int $id, User $user): JsonResponse
    {
        $slot = InspectionSlot::where('dealer_id', $user->id)->where('id', $id)->first();

        if (! $slot instanceof InspectionSlot) {
            return $this->errorResponse(null, 'Slot not found', 404);
        }
          $slot->update([
            'status' => $request->status ?? $slot->status,
        ]);

        return $this->successResponse(null, 'Inspection Updated successfully');
    }

    public function updateSlot(SlotRequest $request, int $id, User $user): JsonResponse
    {
        $slot = InspectionSlot::where('dealer_id', $user->id)->where('id', $id)->first();

        if (! $slot instanceof InspectionSlot) {
            return $this->errorResponse(null, 'Slot not found', 404);
        }

        $slot->update([
            'vehicle_id' => $request->vehicle_id ?? $slot->vehicle_id,
            'location_id' => $request->location_id ?? $slot->location_id,
            'inspection_date' => $request->inspection_date ?? $slot->inspection_date,
            'inspection_time' => $request->inspection_time ?? $slot->inspection_time,
        ]);

        return $this->successResponse(null, 'Location updated successfully');
    }

    public function deleteSlot(int $id, User $user): JsonResponse
    {
        $slot = InspectionSlot::where('dealer_id', $user->id)->where('id', $id)->first();

        if (! $slot instanceof InspectionSlot) {
            return $this->errorResponse(null, 'Slot not found', 404);
        }

        $slot->delete();

        return $this->successResponse(null, 'Slot deleted successfully');
    }
}
