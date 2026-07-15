<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\VehicleRequest;
use App\Models\User;
use App\Services\AccountService;
use App\Services\BuyerService;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Http\JsonResponse;

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

    public function setPreference(VehicleRequest $request, #[CurrentUser] User $user): JsonResponse
    {
        return $this->dealerService->setVehiclePrefernce($request,$user);
    }

}
