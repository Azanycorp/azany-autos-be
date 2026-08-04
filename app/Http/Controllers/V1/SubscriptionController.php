<?php

namespace App\Http\Controllers\V1;

use App\Exceptions\SubscriptionException;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\ChangeSubscriptionPlanRequest;
use App\Http\Resources\SubscriptionPlanResource;
use App\Http\Resources\SubscriptionResource;
use App\Models\User;
use App\Services\SubscriptionService;
use App\Traits\HttpResponses;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Http\JsonResponse;

class SubscriptionController extends Controller
{
    use HttpResponses;

    public function __construct(
        private readonly SubscriptionService $subscription_service
    ) {}

    public function index(): JsonResponse
    {
        $plans = $this->subscription_service->index();

        return $this->successResponse(SubscriptionPlanResource::collection($plans), 'Subscription plans fetched successfully');
    }

    public function show(int $id): JsonResponse
    {
        $plan = $this->subscription_service->show($id);

        if (! $plan) {
            return $this->errorResponse(null, 'Subscription plan not found', 404);
        }

        return $this->successResponse(new SubscriptionPlanResource($plan), 'Subscription plan fetched successfully');
    }

    public function upgrade(ChangeSubscriptionPlanRequest $request, #[CurrentUser] User $user): JsonResponse
    {
        try {
            $subscription = $this->subscription_service->upgrade($user->id, $request->validated('plan_id'));

            return $this->successResponse(new SubscriptionResource($subscription), 'Subscription upgraded successfully');
        } catch (SubscriptionException $e) {
            return $this->errorResponse(null, $e->getMessage(), 422);
        }
    }

    public function downgrade(ChangeSubscriptionPlanRequest $request, #[CurrentUser] User $user): JsonResponse
    {
        try {
            $subscription = $this->subscription_service->downgrade($user->id, $request->validated('plan_id'));

            return $this->successResponse(new SubscriptionResource($subscription), 'Downgrade scheduled for your next billing cycle');
        } catch (SubscriptionException $e) {
            return $this->errorResponse(null, $e->getMessage(), 422);
        }
    }

    public function cancel(#[CurrentUser] User $user): JsonResponse
    {
        try {
            $subscription = $this->subscription_service->cancel($user->id);

            return $this->successResponse(new SubscriptionResource($subscription), 'Subscription will be cancelled at the end of your billing cycle');
        } catch (SubscriptionException $e) {
            return $this->errorResponse(null, $e->getMessage(), 422);
        }
    }
}
