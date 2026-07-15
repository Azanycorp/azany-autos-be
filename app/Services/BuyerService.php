<?php

namespace App\Services;
use App\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BuyerService
{
    use HttpResponses;

    public function setVehiclePrefernce(Request $request, int $id): JsonResponse
    {
        $user = $request->user();

        $vehicle = $user->vehicles->find($id);

        if (! $vehicle) {
            return $this->errorResponse(null, 'Vehicle not found', 404);
        }

        return $this->successResponse(new VehicleResource($vehicle), 'Vehicle retrieved successfully');
    }
}
