<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\PasswordRequest;
use App\Http\Requests\V1\PreferenceRequest;
use App\Http\Requests\V1\ProfilePhotoRequest;
use App\Services\AccountService;
use App\Services\BuyerService;
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

    public function updateProfile(Request $request, int $userId): JsonResponse
    {
        return $this->accountService->updateprofile($request, $userId);
    }

    public function updateProfilePhoto(ProfilePhotoRequest $request, int $userId): JsonResponse
    {
        return $this->accountService->updateProfilePhoto($request, $userId);
    }

    public function updatePassword(PasswordRequest $request, int $userId): JsonResponse
    {
        return $this->accountService->updatePassword($request, $userId);
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
