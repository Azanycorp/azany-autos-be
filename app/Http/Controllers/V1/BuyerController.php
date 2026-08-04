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
        private readonly BuyerService $buyer_service
    ) {}

    public function getVehiclePrefernce(#[CurrentUser] User $user): JsonResponse
    {
        return $this->buyer_service->getVehiclePrefernce($user);
    }

    public function setPreference(PreferenceRequest $request, #[CurrentUser] User $user): JsonResponse
    {
        return $this->buyer_service->setVehiclePrefernce($request, $user);
    }

    public function addSavedSearch(SavedSearchRequest $request): JsonResponse
    {
        return $this->buyer_service->addSavedSearch($request);
    }

    public function getSavedSearches(int $user_id): JsonResponse
    {
        return $this->buyer_service->getSavedSearches($user_id);
    }

    public function updateSavedSearch(Request $request, #[CurrentUser] User $user, int $id): JsonResponse
    {
        return $this->buyer_service->updateSavedSearch($request, $user, $id);
    }

    public function viewSavedSearch(#[CurrentUser] User $user, int $id): JsonResponse
    {
        return $this->buyer_service->viewSavedSearch($user, $id);
    }

    public function deleteSavedSearch(#[CurrentUser] User $user, int $id): JsonResponse
    {
        return $this->buyer_service->deleteSavedSearch($user, $id);
    }

    public function runSavedSearch(#[CurrentUser] User $user, int $id): JsonResponse
    {
        return $this->buyer_service->runSavedSearch($user, $id);
    }
}
