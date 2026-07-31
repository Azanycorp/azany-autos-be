<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\PreferenceRequest;
use App\Http\Requests\V1\SavedSearchRequest;
use App\Services\BuyerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BuyerController extends Controller
{
    public function __construct(
        private readonly BuyerService $buyerService
    ) {}

    public function getVehiclePrefernce(Request $request): JsonResponse
    {
        return $this->buyerService->getVehiclePrefernce($request);
    }

    public function setPreference(PreferenceRequest $request): JsonResponse
    {
        return $this->buyerService->setVehiclePrefernce($request);
    }

    public function addSavedSearch(SavedSearchRequest $request): JsonResponse
    {
        return $this->buyerService->addSavedSearch($request);
    }

    public function getSavedSearches(Request $request, int $user_id): JsonResponse
    {
        return $this->buyerService->getSavedSearches($request, $user_id);
    }

    public function updateSavedSearch(Request $request, int $id): JsonResponse
    {
        return $this->buyerService->updateSavedSearch($request, $id);
    }

    public function viewSavedSearch(Request $request, int $id): JsonResponse
    {
        return $this->buyerService->viewSavedSearch($request, $id);
    }

    public function deleteSavedSearch(Request $request, int $id): JsonResponse
    {
        return $this->buyerService->deleteSavedSearch($request, $id);
    }

    public function runSavedSearch(Request $request, int $id): JsonResponse
    {
        return $this->buyerService->runSavedSearch($request, $id);
    }
}
