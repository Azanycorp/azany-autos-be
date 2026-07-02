<?php
namespace App\Services;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;

class AccountService
{
    use HttpResponses;  

    public function profile(): JsonResponse
    {
        $auth = userAuth();

        if (! $auth) {
            return $this->errorResponse(null, 'User not authenticated', 401);
        }

        $user = User::where('id', $auth->id)->first();

        if (! $user) {
            return $this->errorResponse(null, 'User does not exist', 404);
        }

        return $this->successResponse(new UserResource($user), 'User profile');
    }

}
