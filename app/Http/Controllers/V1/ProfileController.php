<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;

class ProfileController extends Controller
{
    use HttpResponses;

    public function __invoke(int $userId): JsonResponse
    {
        $user = User::with(['activeSubscription.plan.features'])->find($userId);

        if (! $user instanceof User) {
            return $this->errorResponse(null, 'User does not exist', 404);
        }

        return $this->successResponse(new UserResource($user), 'User profile');
    }
}
