<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\PreferenceRequest;
use App\Http\Requests\V1\SavedSearchRequest;
use App\Models\User;
use App\Services\BuyerService;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BuyerController extends Controller
{
    public function __construct(
        private readonly BuyerService $buyerService
    ) {}

    public function getVehiclePrefernce(#[CurrentUser] User $user): JsonResponse
    {
        return $this->buyerService->getVehiclePrefernce($user);
    }

    public function setPreference(PreferenceRequest $request, #[CurrentUser] User $user): JsonResponse
    {
        return $this->buyerService->setVehiclePrefernce($request, $user);
    }

    public function addSavedSearch(SavedSearchRequest $request): JsonResponse
    {
        return $this->buyerService->addSavedSearch($request);
    }

    public function getSavedSearches(int $userId): JsonResponse
    {
        return $this->buyerService->getSavedSearches($userId);
    }

    public function updateSavedSearch(Request $request, #[CurrentUser] User $user, int $id): JsonResponse
    {
        return $this->buyerService->updateSavedSearch($request, $user, $id);
    }

    public function viewSavedSearch(#[CurrentUser] User $user, int $id): JsonResponse
    {
        return $this->buyerService->viewSavedSearch($user, $id);
    }

    public function deleteSavedSearch(#[CurrentUser] User $user, int $id): JsonResponse
    {
        return $this->buyerService->deleteSavedSearch($user, $id);
    }

    public function runSavedSearch(#[CurrentUser] User $user, int $id): JsonResponse
    {
        return $this->buyerService->runSavedSearch($user, $id);
    }
}
