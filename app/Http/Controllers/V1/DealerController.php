<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
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

    public function profile(int $user_id): JsonResponse
    {
        return $this->accountService->profile($user_id);
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
        return $this->dealerService->deleteVehicleImage((int) $request->vehicle_id, $id);
    }

    public function addCustomTag(TagRequest $request): JsonResponse
    {
        return $this->dealerService->addCustomTag($request);
    }

    public function getTags(int $user_id): JsonResponse
    {
        return $this->dealerService->getTags($user_id);
    }

    public function getTag(Request $request, int $id): JsonResponse
    {
        return $this->dealerService->getTag($request, $id);
    }

    public function updateTag(Request $request, int $id): JsonResponse
    {
        return $this->dealerService->updateTag($request, $id);
    }

    public function deleteTag(Request $request, int $id): JsonResponse
    {
        return $this->dealerService->deleteTag($request, $id);
    }
}
