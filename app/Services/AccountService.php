<?php

namespace App\Services;

use App\Http\Requests\V1\ProfilePhotoRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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

    public function updateProfile(Request $request, int $userId): JsonResponse
    {
        $user = User::find($userId);

        if (! $user instanceof User) {
            return $this->errorResponse(null, 'User does not exist', 404);
        }
        $currency_code = getCurrencyCodeByCountryId($request->country_id);

        $user->update([
            'email' => $request->email ?? $user->email,
            'first_name' => $request->first_name ?? $user->first_name,
            'last_name' => $request->last_name ?? $user->last_name,
            'country_id' => $request->country_id ?? $user->country_id,
            'default_currency' => $currency_code ?? $user->default_currency,
        ]);

        return $this->successResponse(null, 'Details Updated');
    }

    public function updateProfilePhoto(ProfilePhotoRequest $request, int $userId): JsonResponse
    {
        $user = User::find($userId);

        if (! $user instanceof User) {
            return $this->errorResponse(null, 'User does not exist', 404);
        }

        $profile_photo = $request->hasFile('profile_photo') ? uploadImage($request->file('profile_photo'), 'profile_photo') : $user->profile_photo;

        $user->update([
            'profile_photo' => $profile_photo,
        ]);

        return $this->successResponse(null, 'Profile photo Updated');
    }
}
