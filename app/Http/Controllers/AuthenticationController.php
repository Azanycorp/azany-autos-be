<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
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

    public function logout()
    {
        return $this->authService->logout();
    }

    public function verifyOtp(Request $request)
    {
        return $this->authService->verifyOtp($request);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email']
        ]);

        return $this->authService->resetPassword($request);
    }

    public function reset(ResetRequest $request)
    {

        return $this->authService->reset($request);
    }

    public function resendVerificationEmail(ResendEmailRequest $request)
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
