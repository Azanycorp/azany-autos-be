<?php

namespace App\Services;

use App\Http\Requests\V1\PreferenceRequest;
use App\Http\Requests\V1\SavedSearchRequest;
use App\Http\Resources\BuyerPreferenceResource;
use App\Http\Resources\SavedSearchResource;
use App\Http\Resources\VehicleResource;
use App\Models\SavedSearch;
use App\Models\User;
use App\Traits\HttpResponses;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BuyerService
{
    use HttpResponses;

    public function getVehiclePrefernce(User $user): JsonResponse
    {
        $preference = $user->vehiclePreference;

        return $this->successResponse(new BuyerPreferenceResource($preference), 'Preference retrieved successfully');
    }

    public function setVehiclePrefernce(PreferenceRequest $request, User $user): JsonResponse
    {
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

    public function getSavedSearches(int $userId): JsonResponse
    {
        $user = User::find($userId);

        if (! $user) {
            return $this->errorResponse(null, 'User not found', 404);
        }
        /** @var Collection<int, SavedSearch> $savedSearches */
        $savedSearches = $user->savedSearches()->latest()->get();

        $totalSaved = $savedSearches->count();
        $alertsOn = $savedSearches->where('is_notify', true)->count();
        $alertsPaused = $savedSearches->where('is_notify', false)->count();

        $newMatchesToday = $savedSearches->sum(
            fn (SavedSearch $search): int => (int) $search->new_matches_today
        );

        $searchesWithNewMatches = $savedSearches->filter(
            fn (SavedSearch $search): bool => (int) $search->new_matches_today > 0
        )->count();

        $totalMatchesFound = $savedSearches->sum(
            fn (SavedSearch $search): int => (int) $search->total_matches
        );

        $data = [
            'total_saved' => $totalSaved,
            'alerts_on' => $alertsOn,
            'alerts_paused' => $alertsPaused,
            'new_matches_today' => $newMatchesToday,
            'searches_with_new_matches' => $searchesWithNewMatches,
            'total_matches_found' => $totalMatchesFound,
            'searches' => SavedSearchResource::collection($savedSearches),
        ];

        return $this->successResponse($data, 'My Saved Searches');

    }

    public function addSavedSearch(SavedSearchRequest $request): JsonResponse
    {
        /** @var User|null $user */
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

    public function viewSavedSearch(User $user, int $id): JsonResponse
    {
        $savedSearch = $user->savedSearches()->find($id);

        if (! $savedSearch) {
            return $this->errorResponse(null, 'Record not found', 404);
        }

        return $this->successResponse(new SavedSearchResource($savedSearch), 'Details');
    }

    public function updateSavedSearch(Request $request, User $user, int $id): JsonResponse
    {
        $savedSearch = $user->savedSearches()->find($id);

        if (! $savedSearch) {
            return $this->errorResponse(null, 'Record not found', 404);
        }

        $savedSearch->update(
            [
                'name' => $request->name ?? $savedSearch->name,
                'listing_type' => $request->listing_type ?? $savedSearch->listing_type,
                'fuel_type' => $request->fuel_type ?? $savedSearch->fuel_type,
                'transmission_type' => $request->transmission_type ?? $savedSearch->transmission_type,
                'condition' => $request->condition ?? $savedSearch->condition,
                'kilometer_reading' => $request->kilometer_reading ?? $savedSearch->kilometer_reading,
                'make' => $request->make ?? $savedSearch->make,
                'model' => $request->model ?? $savedSearch->model,
                'min_year' => $request->min_year ?? $savedSearch->min_year,
                'max_year' => $request->max_year ?? $savedSearch->max_year,
                'min_price' => $request->min_price ?? $savedSearch->min_price,
                'max_price' => $request->max_price ?? $savedSearch->max_price,
                'country_id' => $request->country_id ?? $savedSearch->country_id,
                'body_type' => $request->body_type ?? $savedSearch->body_type,
            ]);

        return $this->successResponse(new SavedSearchResource($savedSearch), 'Details Updated');
    }

    public function deleteSavedSearch(User $user, int $id): JsonResponse
    {
        $savedSearch = $user->savedSearches()->find($id);

        if (! $savedSearch) {
            return $this->errorResponse(null, 'Record not found', 404);
        }

        $savedSearch->delete();

        return $this->successResponse(null, 'Record deleted');
    }

    public function runSavedSearch(User $user, int $id): JsonResponse
    {
        $savedSearch = $user
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
