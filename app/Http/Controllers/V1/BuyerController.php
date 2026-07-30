<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\PasswordRequest;
use App\Http\Requests\V1\PreferenceRequest;
use App\Http\Requests\V1\ProfilePhotoRequest;
use App\Http\Requests\V1\ProfileUpdateRequest;
use App\Http\Requests\V1\SavedSearchRequest;
use App\Http\Requests\V1\Update2FARequest;
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

    public function updateProfile(ProfileUpdateRequest $request): JsonResponse
    {
        return $this->accountService->updateprofile($request);
    }

    public function updateProfilePhoto(ProfilePhotoRequest $request): JsonResponse
    {
        return $this->accountService->updateProfilePhoto($request);
    }

    public function updatePassword(PasswordRequest $request): JsonResponse
    {
        return $this->accountService->updatePassword($request);
    }

    public function enable2FA(Update2FARequest $request): JsonResponse
    {
        return $this->accountService->enable2FA($request);
    }

    public function getVehiclePrefernce(Request $request): JsonResponse
    {
        return $this->dealerService->getVehiclePrefernce($request);
    }

    public function setPreference(PreferenceRequest $request): JsonResponse
    {
        return $this->dealerService->setVehiclePrefernce($request);
    }

    public function addSavedSearch(SavedSearchRequest $request): JsonResponse
    {
        return $this->dealerService->addSavedSearch($request);
    }

    public function getSavedSearches(Request $request, int $user_id): JsonResponse
    {
        return $this->dealerService->getSavedSearches($request,$user_id);
    }
}
