<?php

namespace App\Services;

use App\Http\Requests\V1\PreferenceRequest;
use App\Http\Resources\BuyerPreferenceResource;
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

    public function getSavedSearches(PreferenceRequest $request): JsonResponse
    {
        $user = $request->user();

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

        return $this->successResponse(null, 'My Saved Searches');
    }

    public function addSavedSearch(PreferenceRequest $request): JsonResponse
    {
        $user = $request->user();

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
}
