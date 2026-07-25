<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\SubscriptionPlanResource;
use App\Services\SubscriptionService;
use App\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;

class SubscriptionController extends Controller
{
    use HttpResponses;

    public function __construct(
        private readonly SubscriptionService $subscriptionService
    ) {}

    public function index(): JsonResponse
    {
        $plans = $this->subscriptionService->index();

        return $this->successResponse(SubscriptionPlanResource::collection($plans), 'Subscription plans fetched successfully');
    }

    public function show(int $id): JsonResponse
    {
        $plan = $this->subscriptionService->show($id);

        if (! $plan) {
            return $this->errorResponse(null, 'Subscription plan not found', 404);
        }

        return $this->successResponse(new SubscriptionPlanResource($plan), 'Subscription plan fetched successfully');
    }
}
