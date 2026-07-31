<?php

namespace App\Services;

use App\Http\Requests\V1\PasswordRequest;
use App\Http\Requests\V1\ProfilePhotoRequest;
use App\Http\Requests\V1\ProfileUpdateRequest;
use App\Http\Requests\V1\Update2FARequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;

class ProfileService
{
    use HttpResponses;

    public function profile(int $userId): JsonResponse
    {
        $user = User::with(['activeSubscription.plan.features'])->find($userId);

        if (! $user instanceof User) {
            return $this->errorResponse(null, 'User does not exist', 404);
        }

        return $this->successResponse(new UserResource($user), 'User profile');
    }

    public function updateProfile(ProfileUpdateRequest $request): JsonResponse
    {
        $user = $request->user();

        if (! $user instanceof User) {
            return $this->errorResponse(null, 'User does not exist', 404);
        }

        $currency_code = $request->filled('country_id')
        ? getCurrencyCodeByCountryId((int) $request->country_id)
        : $user->default_currency;

        $user->update([
            'email' => $request->email ?? $user->email,
            'first_name' => $request->first_name ?? $user->first_name,
            'last_name' => $request->last_name ?? $user->last_name,
            'country_id' => $request->country_id ?? $user->country_id,
            'default_currency' => $currency_code,
        ]);

        return $this->successResponse(null, 'Details Updated');
    }

    public function updateProfilePhoto(ProfilePhotoRequest $request): JsonResponse
    {
        $user = $request->user();

        if (! $user instanceof User) {
            return $this->errorResponse(null, 'User does not exist', 404);
        }

        $profile_photo = $request->hasFile('profile_photo') ? uploadImage($request->file('profile_photo'), 'profile_photo') : $user->profile_photo;

        $user->update([
            'profile_photo' => $profile_photo,
        ]);

        return $this->successResponse(null, 'Profile photo Updated');
    }

    public function updatePassword(PasswordRequest $request): JsonResponse
    {
        $user = $request->user();

        if (! $user instanceof User) {
            return $this->errorResponse(null, 'User does not exist', 404);
        }

        $user->update([
            'password' => $request->password,
        ]);

        return $this->successResponse(null, 'Password Updated');
    }

    public function enable2FA(Update2FARequest $request): JsonResponse
    {
        $user = $request->user();

        $newStatus = (bool) $request->validated('two_factor_enabled');

        if ($user->two_factor_enabled === $newStatus) {
            $state = $newStatus ? 'enabled' : 'disabled';

            return $this->errorResponse(null, "2FA is already {$state}.", 400);
        }

        $user->update([
            'two_factor_enabled' => $newStatus,
        ]);

        $status = $newStatus ? 'enabled' : 'disabled';

        return $this->successResponse(null, "2FA {$status} successfully.");
    }
}
