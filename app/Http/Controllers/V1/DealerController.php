<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\LocationRequest;
use App\Http\Requests\V1\SlotRequest;
use App\Http\Requests\V1\TagRequest;
use App\Http\Requests\V1\UpdateVehicleRequest;
use App\Http\Requests\V1\VehicleRequest;
use App\Models\User;
use App\Services\AccountService;
use App\Services\DealerService;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DealerController extends Controller
{
    public function __construct(
        private readonly AccountService $accountService,
        private readonly DealerService $dealerService
    ) {}

    public function profile(int $userId): JsonResponse
    {
        return $this->accountService->profile($userId);
    }

    public function addVehicle(VehicleRequest $request): JsonResponse
    {
        return $this->dealerService->addVehicle($request);
    }

    public function getVehicles(Request $request): JsonResponse
    {
        return $this->dealerService->getVehicles($request);
    }

    public function getVehicle(Request $request, int $id): JsonResponse
    {
        return $this->dealerService->getVehicle($request, $id);
    }

    public function updateVehicle(UpdateVehicleRequest $request, int $id): JsonResponse
    {
        return $this->dealerService->updateVehicle($request, $id);
    }

    public function deleteVehicle(Request $request, int $id): JsonResponse
    {
        return $this->dealerService->deleteVehicle($request, $id);
    }

    public function updateVehicleStatus(Request $request, int $id): JsonResponse
    {
        return $this->dealerService->updateVehicleStatus($request, $id);
    }

    public function deleteVehicleImage(Request $request, int $id): JsonResponse
    {
        return $this->dealerService->deleteVehicleImage($request, $id);
    }

    public function addCustomTag(TagRequest $request, #[CurrentUser] User $user): JsonResponse
    {
        return $this->dealerService->addCustomTag($request, $user);
    }

    public function getTags(int $userId): JsonResponse
    {
        return $this->dealerService->getTags($userId);
    }

    public function getTag(int $id, #[CurrentUser] User $user): JsonResponse
    {
        return $this->dealerService->getTag($id, $user);
    }

    public function updateTag(Request $request, int $id, #[CurrentUser] User $user): JsonResponse
    {
        return $this->dealerService->updateTag($request, $id, $user);
    }

    public function deleteTag(int $id, #[CurrentUser] User $user): JsonResponse
    {
        return $this->dealerService->deleteTag($id, $user);
    }

    public function addNewLocation(LocationRequest $request, #[CurrentUser] User $user): JsonResponse
    {
        return $this->dealerService->addNewLocation($request, $user);
    }

    public function getAllLocations(int $userId): JsonResponse
    {
        return $this->dealerService->getAllLocations($userId);
    }

    public function getLocation(int $id, #[CurrentUser] User $user): JsonResponse
    {
        return $this->dealerService->getLocation($id, $user);
    }

    public function updateLocation(Request $request, int $id, #[CurrentUser] User $user): JsonResponse
    {
        return $this->dealerService->updateLocation($request, $id, $user);
    }

    public function makeLocationDefault(int $id, #[CurrentUser] User $user): JsonResponse
    {
        return $this->dealerService->makeLocationDefault($id, $user);
    }

    public function deleteLocation(int $id, #[CurrentUser] User $user): JsonResponse
    {
        return $this->dealerService->deleteLocation($id, $user);
    }

    public function addNewSlot(SlotRequest $request, #[CurrentUser] User $user): JsonResponse
    {
        return $this->dealerService->addNewSlot($request, $user);
    }

    public function getAllSlots(int $userId): JsonResponse
    {
        return $this->dealerService->getAllSlots($userId);
    }

    public function getSlot(int $id, #[CurrentUser] User $user): JsonResponse
    {
        return $this->dealerService->getSlot($id, $user);
    }

    public function updateSlotStatus(int $id, #[CurrentUser] User $user): JsonResponse
    {
        return $this->dealerService->updateSlotStatus($id, $user);
    }

    public function updateSlot(SlotRequest $request, int $id, #[CurrentUser] User $user): JsonResponse
    {
        return $this->dealerService->updateSlot($request, $id, $user);
    }

    public function deleteSlot(int $id, #[CurrentUser] User $user): JsonResponse
    {
        return $this->dealerService->deleteLocation($id, $user);
    }
}
