<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\LocationRequest;
use App\Http\Requests\V1\SlotRequest;
use App\Http\Requests\V1\StatusUpdateRequest;
use App\Http\Requests\V1\TagRequest;
use App\Http\Requests\V1\UpdateVehicleRequest;
use App\Http\Requests\V1\VehicleRequest;
use App\Models\User;
use App\Services\DealerService;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DealerController extends Controller
{
    public function __construct(private readonly DealerService $dealer_service) {}

    public function dashboard(int $user_id): JsonResponse
    {
        return $this->dealer_service->dashboard($user_id);
    }

    public function addVehicle(VehicleRequest $request): JsonResponse
    {
        return $this->dealer_service->addVehicle($request);
    }

    public function getVehicles(Request $request): JsonResponse
    {
        return $this->dealer_service->getVehicles($request);
    }

    public function getVehicle(Request $request, int $id): JsonResponse
    {
        return $this->dealer_service->getVehicle($request, $id);
    }

    public function updateVehicle(UpdateVehicleRequest $request, int $id): JsonResponse
    {
        return $this->dealer_service->updateVehicle($request, $id);
    }

    public function deleteVehicle(#[CurrentUser] User $user, int $id): JsonResponse
    {
        return $this->dealer_service->deleteVehicle($user, $id);
    }

    public function updateVehicleStatus(Request $request, #[CurrentUser] User $user, int $id): JsonResponse
    {
        return $this->dealer_service->updateVehicleStatus($request, $user, $id);
    }

    public function deleteVehicleImage(Request $request, int $id): JsonResponse
    {
        return $this->dealer_service->deleteVehicleImage($request, $id);
    }

    public function addCustomTag(TagRequest $request, #[CurrentUser] User $user): JsonResponse
    {
        return $this->dealer_service->addCustomTag($request, $user);
    }

    public function getTags(int $userId): JsonResponse
    {
        return $this->dealer_service->getTags($userId);
    }

    public function getTag(int $id, #[CurrentUser] User $user): JsonResponse
    {
        return $this->dealer_service->getTag($id, $user);
    }

    public function updateTag(Request $request, int $id, #[CurrentUser] User $user): JsonResponse
    {
        return $this->dealer_service->updateTag($request, $id, $user);
    }

    public function deleteTag(int $id, #[CurrentUser] User $user): JsonResponse
    {
        return $this->dealer_service->deleteTag($id, $user);
    }

    public function addNewLocation(LocationRequest $request, #[CurrentUser] User $user): JsonResponse
    {
        return $this->dealer_service->addNewLocation($request, $user);
    }

    public function getAllLocations(int $userId): JsonResponse
    {
        return $this->dealer_service->getAllLocations($userId);
    }

    public function getLocation(int $id, #[CurrentUser] User $user): JsonResponse
    {
        return $this->dealer_service->getLocation($id, $user);
    }

    public function updateLocation(Request $request, int $id, #[CurrentUser] User $user): JsonResponse
    {
        return $this->dealer_service->updateLocation($request, $id, $user);
    }

    public function makeLocationDefault(int $id, #[CurrentUser] User $user): JsonResponse
    {
        return $this->dealer_service->makeLocationDefault($id, $user);
    }

    public function deleteLocation(int $id, #[CurrentUser] User $user): JsonResponse
    {
        return $this->dealer_service->deleteLocation($id, $user);
    }

    public function addNewSlot(SlotRequest $request, #[CurrentUser] User $user): JsonResponse
    {
        return $this->dealer_service->addNewSlot($request, $user);
    }

    public function getAllSlots(int $userId): JsonResponse
    {
        return $this->dealer_service->getAllSlots($userId);
    }

    public function getSlot(int $id, #[CurrentUser] User $user): JsonResponse
    {
        return $this->dealer_service->getSlot($id, $user);
    }

    public function updateSlotStatus(StatusUpdateRequest $request, int $id, #[CurrentUser] User $user): JsonResponse
    {
        return $this->dealer_service->updateSlotStatus($request, $id, $user);
    }

    public function updateSlot(SlotRequest $request, int $id, #[CurrentUser] User $user): JsonResponse
    {
        return $this->dealer_service->updateSlot($request, $id, $user);
    }

    public function deleteSlot(int $id, #[CurrentUser] User $user): JsonResponse
    {
        return $this->dealer_service->deleteSlot($id, $user);
    }
}
