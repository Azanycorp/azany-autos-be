<?php

namespace App\Services;

use App\Enum\MailingEnum;
use App\Enum\UserStatus;
use App\Enum\UserType;
use App\Http\Requests\CodeRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\ResetRequest;
use App\Http\Requests\VerifyUserRequest;
use App\Http\Resources\LoginResource;
use App\Http\Resources\UserResource;
use App\Mail\PasswordResetCodeMail;
use App\Mail\TwoFACodeMail;
use App\Models\User;
use App\Models\Verify;
use App\Services\Auth\HttpService;
use App\Traits\HttpResponses;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;

class AuthService
{
    use HttpResponses;

    public function __construct(
        private HttpService $httpService
    ) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            $requestData = $request->only([
                'first_name',
                'last_name',
                'email',
                'country_id',
                'password',
            ]);

            $requestData['signed_up_from'] = 'Azanyautos';
            $requestData['type'] = UserType::AUTOBUYER->value;

            $response = $this->httpService->register($requestData)->throw();
            $user = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'user_type' => $request->user_type,
                'reg_number' => $request->reg_number,
                'business_name' => $request->business_name,
                'contact_person' => $request->contact_person,
                'country_id' => $request->country_id,
                'status' => UserStatus::PENDING->value,
                'password' => bcrypt($request->password),
            ]);

            $user->sendVerificationEmail();

            return $this->successResponse(
                new UserResource($user),
                'Registration successful. Kindly check your inbox for instructions on how to verify your account. Thanks.'
            );

        } catch (RequestException $e) {
            return $this->errorResponse(
                $e->response->json(),
                null,
                $e->response->status()
            );

        } catch (\Exception $e) {
            return $this->errorResponse(null, 'An unexpected error occurred during registration.', 400);
        }
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->only(['email', 'password']);

        if (! Auth::attempt($credentials)) {
            return $this->errorResponse(null, 'Credentials do not match', 401);
        }

        $response = $this->httpService->login($credentials);
        $user = User::where('email', $request->email)->firstOrFail();

        if ($response->successful()) {
            return $this->handleSuccessfulLogin($user);
        }
        if ($response->status() === 401) {
            return $this->syncUserWithAuthService($user, $request->password, $request->email);
        }

        return $this->errorResponse(null, $response->json()['message'] ?? 'Auth service failure', $response->status());
    }

    private function handleSuccessfulLogin(User $user): JsonResponse
    {
        if ($user->two_factor_enabled) {
            $verificationCode = generateUserVerificationCode();
            $user->update(['verification_code' => $verificationCode, 'verification_code_expire_at' => now()->addMinutes(30)]);

            $type = MailingEnum::TWO_FA_OTP;
            $subject = 'Two-Factor Authentication Code';
            $mail_class = TwoFACodeMail::class;
            $data = [
                'user' => $user,
            ];
            mailSend($type, $user, $subject, $mail_class, $data);

            return $this->successResponse(null, '2FA code sent.');
        }

        return $this->issueToken($user);
    }

    private function syncUserWithAuthService(User $user, string $password, string $email): JsonResponse
    {
        $creation = $this->httpService->register(array_merge(
            $user->only(['first_name', 'last_name', 'email', 'country_id']),
            [
                'password' => $password,
                'signed_up_from' => 'Azanyautos',
                'type' => UserType::AUTOBUYER->value,
                'email_verified_at' => now(),
                'is_verified' => true,
                'status' => UserStatus::ACTIVE->value,
            ]
        ));

        if ($creation->successful() && $this->httpService->login(['email' => $email, 'password' => $password])->successful()) {
            return $this->issueToken($user);
        }

        return $this->errorResponse(
            null,
            $creation->json()['message'] ?? 'Synchronization failed.',
            $creation->status()
        );
    }

    public function verifyOtp(Request $request): JsonResponse
    {
        $verify = Verify::with('user')
            ->where('token', $request->code)
            ->where('expires_at', '>', now())
            ->firstOrFail();

        $user = $verify->user;
        if (! $user) {
            return $this->errorResponse(null, 'Associated user account could not be found.', 404);
        }
        $response = $this->httpService->verifyCode($user->email);

        if ($response->successful()) {
            $user->update([
                'email_verified_at' => now(),
                'status' => UserStatus::ACTIVE->value,
            ]);

            $verify->delete();

            $token = $user->createToken($user->email, ['*'], now()->addDays(5));

            $data = (object) [
                'user' => new UserResource($user),
                'token' => $token->plainTextToken,
            ];

            return $this->successResponse($data, 'Account verified successfully.');
        } else {
            return $this->errorResponse($response->json()['message'], null, $response->status());
        }
    }

    public function resetPassword(VerifyUserRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->firstOrFail();

        $result = $user->sendPasswordResetEmail();

        $message = ($result instanceof Verify) ? 'Reset email sent.' : $result;

        return $this->successResponse($message);
    }

    public function reset(ResetRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();

        if (! $user) {
            return $this->errorResponse(null, 'User not found', 404);
        }

        try {
            $token = decrypt($request->token);
        } catch (\Exception $e) {
            return $this->errorResponse(null, 'Invalid token', 403);
        }

        $verify = Verify::where('email', $request->email)
            ->where('token', $token)
            ->first();

        if (! $verify) {
            return $this->errorResponse(null, 'Invalid or expired token', 403);
        }

        $user->verify($verify, $request->password);

        return $this->successResponse(null, 'Updated successfully');
    }

    public function resendVerificationEmail(VerifyUserRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->firstOrFail();

        if (filled($user->email_verified_at)) {
            return $this->errorResponse(null, 'Email already verified.', 422);
        }

        $user->sendVerificationEmail();

        return $this->successResponse(null, 'Verification email resent.');
    }

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

    public function verify2fa(CodeRequest $request): JsonResponse
    {
        $user = User::where('verification_code', $request->verification_code)->first();

        if (! $user) {
            return $this->errorResponse(null, 'Invalid code entered, please try it again.', 403);
        }

        if ($user->verification_code_expire_at < now()) {
            return $this->errorResponse(null, 'Verification Code has Expired!', 422);
        }

        $user->update([
            'verification_code' => null,
            'verification_code_expire_at' => null,
        ]);

        return $this->issueToken($user);
    }

    protected function issueToken(User $user): JsonResponse
    {
        $token = $user->createToken($user->email);

        return new JsonResponse([
            'user' => (new LoginResource($user))->resolve(),
            'token' => $token->plainTextToken,
        ]);
    }

    // forgot password section
    public function resendCode(Request $request): JsonResponse
    {
        $user = User::where('email', $request->email)->firstOrFail();

        $verificationCode = generateUserVerificationCode();
        $expiry = now()->addMinutes(30);

        $user->update([
            'verification_code' => $verificationCode,
            'verification_code_expire_at' => $expiry,
        ]);

        $type = MailingEnum::RESET_OTP;
        $subject = 'Password Reset Request';
        $mail_class = PasswordResetCodeMail::class;
        $data = [
            'user' => $user,
        ];
        mailSend($type, $user, $subject, $mail_class, $data);

        return $this->successResponse(null, 'A new code has been sent to you');
    }

    public function verifyUserIdentity(Request $request): JsonResponse
    {
        $user = User::where('email', $request->email)->firstOrFail();
        $verificationCode = generateUserVerificationCode();

        $user->update([
            'verification_code' => $verificationCode,
            'verification_code_expire_at' => Date::now()->addMinutes(30),
        ]);

        $type = MailingEnum::RESET_OTP;
        $subject = 'Password Reset Request';
        $mail_class = PasswordResetCodeMail::class;
        $data = [
            'user' => $user,
        ];
        mailSend($type, $user, $subject, $mail_class, $data);

        return $this->successResponse(null, 'A verification code has been sent to your email');
    }

    public function verifyCode(Request $request): JsonResponse
    {
        $user = User::where('verification_code', $request->verification_code)->first();
        if (! $user) {
            return $this->errorResponse(null, 'Invalid code entered, please try it again.', 422);
        }

        if ($user->verification_code_expire_at < now()) {
            return $this->errorResponse(null, 'Verification Code has Expired!', 422);
        }
        $user->update([
            'verification_code' => null,
            'verification_code_expire_at' => null,
        ]);

        return $this->successResponse(null, 'Code Verified');
    }

    public function changePassword(Request $request): JsonResponse
    {
        $user = User::where('email', $request->email)->firstOrFail();

        $user->update([
            'password' => bcrypt($request->password),
        ]);

        return $this->successResponse(null, 'Password Reset successfully!');
    }
}
