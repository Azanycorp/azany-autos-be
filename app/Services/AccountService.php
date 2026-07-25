<?php

namespace App\Services;

use App\Http\Resources\UserResource;
use App\Models\User;
use App\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AccountService
{
    use HttpResponses;

    public function profile(int $userId): JsonResponse
    {
        $user = User::find($userId);

        if (! $user instanceof User) {
            return $this->errorResponse(null, 'User does not exist', 404);
        }

        return $this->successResponse(new UserResource($user), 'User profile');
    }
}
