<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\PreferenceRequest;
use App\Http\Requests\V1\VehicleRequest;
use App\Models\User;
use App\Services\AccountService;
use App\Services\BuyerService;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BuyerController extends Controller
{
    public function __construct(
        private readonly AccountService $accountService,
        private readonly BuyerService $dealerService
    ) {}

    public function profile(int $userId): JsonResponse
    {
        return $this->accountService->profile($userId);
    }

    public function getVehiclePrefernce(Request $request): JsonResponse
    {
        return $this->dealerService->getVehiclePrefernce($request);
    }

    public function setPreference(PreferenceRequest $request): JsonResponse
    {
        return $this->dealerService->setVehiclePrefernce($request);
    }

}
