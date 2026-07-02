<?php
namespace App\Http\Controllers\V1;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\VehicleRequest;
use App\Services\AccountService;
use App\Services\DealerService;
use Illuminate\Http\JsonResponse;

class DealerController extends Controller
{
    public function __construct(
        private readonly AccountService $accountService,
        private readonly DealerService $dealerService
    ) {}

    public function profile(): JsonResponse
    {
        return $this->accountService->profile();
    }

    public function addVehicle(VehicleRequest $request): JsonResponse
    {
        return $this->dealerService->addVehicle($request);
    }
}
