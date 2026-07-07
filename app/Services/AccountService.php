<?php
namespace App\Services;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;

class AccountService
{
    use HttpResponses;

    public function profile(int $user_id): JsonResponse
    {
        $user = User::where('id', $user_id)->first();

        if (! $user) {
            return $this->errorResponse(null, 'User does not exist', 404);
        }

        return $this->successResponse(new UserResource($user), 'User profile');
    }

}
