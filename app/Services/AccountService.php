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
        $user = User::where('id', $userId)->first();

        if (! $user) {
            return $this->errorResponse(null, 'User does not exist', 404);
        }

        return $this->successResponse(new UserResource($user), 'User profile');
    }

    public function updateBuyerProfile(Request $request, User $user): JsonResponse
    {
        $profile_photo = $request->hasFile('profile_photo') ? uploadImage($request->file('profile_photo'), 'profile') : $user->profile_photo;
        $currency_code = getCurrencyCodeByCountryId($request->country_id);

        $user->update([
            'email' => $request->email ?? $user->email,
            'first_name' => $request->first_name ?? $user->first_name,
            'last_name' => $request->last_name ?? $user->last_name,
            'country_id' => $request->country_id ?? $user->country_id,
            'default_currency' => $currency_code ?? $user->default_currency,
            'phone' => $request->phone ?? $user->phone,
            'profile_photo' => $profile_photo,
        ]);

        return $this->successResponse(null, 'Profile updated');
    }

    public function updatePassword(Request $request, User $user): JsonResponse
    {
        if (! Hash::check($request->old_password, $user->password)) {
            return $this->errorResponse(null, 'Old password is incorrect.', 400);
        }

        $user->update([
            'password' => bcrypt($request->password),
        ]);

        return $this->successResponse(null, 'Profile updated');
    }
}
