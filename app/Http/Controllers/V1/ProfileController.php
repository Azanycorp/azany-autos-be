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
        private readonly ProfileService $profileService
    ) {}

    public function profile(int $userId): JsonResponse
    {
        return $this->profileService->profile($userId);
    }

    public function updateProfile(ProfileUpdateRequest $request, #[CurrentUser] User $user): JsonResponse
    {
        return $this->profileService->updateProfile($request, $user);
    }

    public function updateProfilePhoto(ProfilePhotoRequest $request, #[CurrentUser] User $user): JsonResponse
    {
        return $this->profileService->updateProfilePhoto($request, $user);
    }

    public function updatePassword(PasswordRequest $request, #[CurrentUser] User $user): JsonResponse
    {
        return $this->profileService->updatePassword($request, $user);
    }

    public function enable2FA(Update2FARequest $request, #[CurrentUser] User $user): JsonResponse
    {
        return $this->profileService->enable2FA($request, $user);
    }
}
