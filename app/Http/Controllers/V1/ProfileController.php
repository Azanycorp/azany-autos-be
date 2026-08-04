<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\PasswordRequest;
use App\Http\Requests\V1\ProfilePhotoRequest;
use App\Http\Requests\V1\ProfileUpdateRequest;
use App\Http\Requests\V1\Update2FARequest;
use App\Models\User;
use App\Services\ProfileService;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Http\JsonResponse;

class ProfileController extends Controller
{
    public function __construct(
        private readonly ProfileService $profile_service
    ) {}

    public function profile(int $user_id): JsonResponse
    {
        return $this->profile_service->profile($user_id);
    }

    public function updateProfile(ProfileUpdateRequest $request, #[CurrentUser] User $user): JsonResponse
    {
        return $this->profile_service->updateProfile($request, $user);
    }

    public function updateProfilePhoto(ProfilePhotoRequest $request, #[CurrentUser] User $user): JsonResponse
    {
        return $this->profile_service->updateProfilePhoto($request, $user);
    }

    public function updatePassword(PasswordRequest $request, #[CurrentUser] User $user): JsonResponse
    {
        return $this->profile_service->updatePassword($request, $user);
    }

    public function enable2FA(Update2FARequest $request, #[CurrentUser] User $user): JsonResponse
    {
        return $this->profile_service->enable2FA($request, $user);
    }
}
