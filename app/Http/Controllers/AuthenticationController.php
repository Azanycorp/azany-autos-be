<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\CodeRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\ResetRequest;
use App\Http\Requests\VerifyUserRequest;
use App\Services\AuthService;
use Illuminate\Http\Request;


class AuthenticationController extends Controller
{
    public function __construct(
        private AuthService $authService
    ) {}

    public function register(RegisterRequest $request)
    {
        return $this->authService->register($request);
    }

    public function login(LoginRequest $request)
    {
        return $this->authService->login($request);
    }

     public function verify2fa(CodeRequest $request)
    {
        return $this->authService->verify2fa($request);
    }


    public function verifyOtp(Request $request)
    {
        return $this->authService->verifyOtp($request);
    }

    public function resetPassword(VerifyUserRequest $request)
    {
        return $this->authService->resetPassword($request);
    }

    public function reset(ResetRequest $request)
    {

        return $this->authService->reset($request);
    }

    public function resendVerificationEmail(VerifyUserRequest $request)
    {
        return $this->authService->resendVerificationEmail($request);
    }

    public function profile()
    {
        return $this->authService->profile();
    }

    public function updatePassword(ChangePasswordRequest $request)
    {
        return $this->authService->updatePassword($request);
    }

}
