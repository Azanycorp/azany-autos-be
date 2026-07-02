<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\UpdateVehicleRequest;
use App\Http\Requests\V1\VehicleRequest;
use App\Services\AccountService;
use App\Services\DealerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DealerController extends Controller
{
    public function __construct(
        private readonly AccountService $accountService,
        private readonly DealerService $dealerService
    ) {}

    public function profile(): JsonResponse
    {
        return $this->accountService->profile();
    }

    public function addVehicle(VehicleRequest $request): JsonResponse
    {
        return $this->dealerService->addVehicle($request);
    }

    public function getVehicles(Request $request): JsonResponse
    {
        return $this->dealerService->getVehicles($request);
    }

    public function getVehicle(int $id): JsonResponse
    {
        return $this->dealerService->getVehicle($id);
    }

    public function updateVehicle(int $id, UpdateVehicleRequest $request): JsonResponse
    {
        return $this->dealerService->updateVehicle($request, $id);
    }

    public function deleteVehicle(int $id): JsonResponse
    {
        return $this->dealerService->deleteVehicle($id);
    }

    public function updateVehicleStatus(Request $request, int $id): JsonResponse
    {
        return $this->dealerService->updateVehicleStatus($request, $id);
    }

    public function deleteVehicleImage(Request $request, int $id): JsonResponse
    {
        return $this->dealerService->deleteVehicleImage($request, $id);
    }
}
