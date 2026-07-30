<?php

namespace App\Services;

use App\Http\Requests\V1\PreferenceRequest;
use App\Http\Requests\V1\SavedSearchRequest;
use App\Http\Resources\BuyerPreferenceResource;
use App\Http\Resources\SavedSearchResource;
use App\Http\Resources\VehicleResource;
use App\Models\User;
use App\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BuyerService
{
    use HttpResponses;

    public function getVehiclePrefernce(Request $request): JsonResponse
    {
        $user = $request->user();

        $preference = $user->vehiclePreference;

        return $this->successResponse(new BuyerPreferenceResource($preference), 'Preference retrieved successfully');
    }

    public function setVehiclePrefernce(PreferenceRequest $request): JsonResponse
    {
        $user = $request->user();

        $user->vehiclePreference()->updateOrCreate(
            [
                'user_id' => $user->id,
            ],
            [
                'vehicles' => $request->vehicles,
                'fuel_types' => $request->fuel_types,
                'budget_min' => $request->budget_min,
                'budget_max' => $request->budget_max,
                'prefered_colors' => $request->prefered_colors,
                'transmissions' => $request->transmissions,
                'body_types' => $request->body_types,
            ]);

        return $this->successResponse(null, 'Preference set successfully');
    }

    public function getSavedSearches(Request $request, int $user_id): JsonResponse
    {
        $user = User::find($user_id);

        if (! $user) {
            return $this->errorResponse(null, 'User not found', 404);
        }

        $savedSearches = $user->savedSearches()->latest()->get();

        $totalSaved = $savedSearches->count();
        $alertsOn = $savedSearches->where('is_notify', true)->count();
        $alertsPaused = $savedSearches->where('is_notify', false)->count();

        $newMatchesToday = $savedSearches->sum(function ($search) {
            return $search->new_matches_today;
        });

        $searchesWithNewMatches = $savedSearches->filter(function ($search) {
            return $search->new_matches_today > 0;
        })->count();

        $totalMatchesFound = $savedSearches->sum(function ($search) {
            return $search->total_matches;
        });

        $data = [
            'stats' => [
                'total_saved' => $totalSaved,
                'alerts_on' => $alertsOn,
                'alerts_paused' => $alertsPaused,
                'new_matches_today' => $newMatchesToday,
                'searches_with_new_matches' => $searchesWithNewMatches,
                'total_matches_found' => $totalMatchesFound,
            ],
            'searches' => SavedSearchResource::collection($savedSearches),
        ];

        return $this->successResponse($data, 'My Saved Searches');
    }

    public function addSavedSearch(SavedSearchRequest $request): JsonResponse
    {
        $user = User::find($request->user_id);

        if (! $user) {
            return $this->errorResponse(null, 'User not found', 404);

        }

        $user->savedSearches()->create(
            [
                'name' => $request->name,
                'listing_type' => $request->listing_type,
                'fuel_type' => $request->fuel_type,
                'transmission_type' => $request->transmission_type,
                'condition' => $request->condition,
                'kilometer_reading' => $request->kilometer_reading,
                'make' => $request->make,
                'model' => $request->model,
                'min_year' => $request->min_year,
                'max_year' => $request->max_year,
                'min_price' => $request->min_price,
                'max_price' => $request->max_price,
                'country_id' => $request->country_id,
                'body_type' => $request->body_type,
            ]);

        return $this->successResponse(null, 'New record added successfully');
    }

    public function viewSavedSearch(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        $saved_search = $user->savedSearches()->find($id);

        if (! $saved_search) {
            return $this->errorResponse(null, 'Record not found', 404);
        }

        return $this->successResponse(new SavedSearchResource($saved_search), 'Details');
    }

    public function updateSavedSearch(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        $saved_search = $user->savedSearches()->find($id);

        if (! $saved_search) {
            return $this->errorResponse(null, 'Record not found', 404);
        }

        $saved_search->update(
            [
                'name' => $request->name ?? $saved_search->name,
                'listing_type' => $request->listing_type ?? $saved_search->listing_type,
                'fuel_type' => $request->fuel_type ?? $saved_search->fuel_type,
                'transmission_type' => $request->transmission_type ?? $saved_search->transmission_type,
                'condition' => $request->condition ?? $saved_search->condition,
                'kilometer_reading' => $request->kilometer_reading ?? $saved_search->kilometer_reading,
                'make' => $request->make ?? $saved_search->make,
                'model' => $request->model ?? $saved_search->model,
                'min_year' => $request->min_year ?? $saved_search->min_year,
                'max_year' => $request->max_year ?? $saved_search->max_year,
                'min_price' => $request->min_price ?? $saved_search->min_price,
                'max_price' => $request->max_price ?? $saved_search->max_price,
                'country_id' => $request->country_id ?? $saved_search->country_id,
                'body_type' => $request->body_type ?? $saved_search->body_type,
            ]);

        return $this->successResponse(new SavedSearchResource($saved_search), 'Details Updated');
    }

    public function deleteSavedSearch(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        $saved_search = $user->savedSearches()->find($id);

        if (! $saved_search) {
            return $this->errorResponse(null, 'Record not found', 404);
        }
        $saved_search->delete();

        return $this->successResponse(null, 'Record deleted');
    }

    public function runSavedSearch(Request $request, int $id): JsonResponse
    {
        $savedSearch = $request->user()
            ->savedSearches()
            ->findOrFail($id);

        $vehicles = $savedSearch->matchesQuery()
            ->latest()
            ->get();

        return $this->successResponse(VehicleResource::collection($vehicles),
            "Matches found for '{$savedSearch->name}'"
        );
    }
}
