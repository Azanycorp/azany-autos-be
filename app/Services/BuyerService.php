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

    public function getSavedSearches(int $user_id): JsonResponse
    {
        $user = User::find($user_id);

        if (! $user) {
            return $this->errorResponse(null, 'User not found', 404);
        }
        /** @var Collection<int, SavedSearch> $savedSearches */
        $saved_searches = $user->savedSearches()->latest()->get();

        $total_saved = $savedSearches->count();
        $alerts_on = $savedSearches->where('is_notify', true)->count();
        $alerts_paused = $savedSearches->where('is_notify', false)->count();

        $new_matches_today = $savedSearches->sum(
            fn (SavedSearch $search): int => (int) $search->new_matches_today
        );

        $searches_with_new_matches = $savedSearches->filter(
            fn (SavedSearch $search): bool => (int) $search->new_matches_today > 0
        )->count();

        $total_matches_found = $savedSearches->sum(
            fn (SavedSearch $search): int => (int) $search->total_matches
        );

        $data = [
            'total_saved' => $total_saved,
            'alerts_on' => $alerts_on,
            'alerts_paused' => $alerts_paused,
            'new_matches_today' => $new_matches_today,
            'searches_with_new_matches' => $searches_with_new_matches,
            'total_matches_found' => $total_matches_found,
            'searches' => SavedSearchResource::collection($saved_searches),
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
        $saved_search = $user->savedSearches()->find($id);

        if (! $saved_search) {
            return $this->errorResponse(null, 'Record not found', 404);
        }

        return $this->successResponse(new SavedSearchResource($saved_search), 'Details');
    }

    public function updateSavedSearch(Request $request, User $user, int $id): JsonResponse
    {
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

    public function deleteSavedSearch(User $user, int $id): JsonResponse
    {
        $saved_search = $user->savedSearches()->find($id);

        if (! $saved_search) {
            return $this->errorResponse(null, 'Record not found', 404);
        }
        
        $saved_search->delete();

        return $this->successResponse(null, 'Record deleted');
    }

    public function runSavedSearch(User $user, int $id): JsonResponse
    {
        $saved_search = $user
            ->savedSearches()
            ->findOrFail($id);

        $vehicles = $saved_search->matchesQuery()
            ->latest()
            ->get();

        return $this->successResponse(VehicleResource::collection($vehicles),
            "Matches found for '{$saved_search->name}'"
        );
    }
}
