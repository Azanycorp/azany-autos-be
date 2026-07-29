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
                'vehicle_ids' => $request->vehicle_ids,
                'fuel_types' => $request->fuel_types,
                'budget_min' => $request->budget_min,
                'budget_max' => $request->budget_max,
                'prefered_colors' => $request->prefered_colors,
                'transmissions' => $request->transmissions,
                'body_types' => $request->body_types,
            ]);

        return $this->successResponse(null, 'Preference set successfully');
    }
}
