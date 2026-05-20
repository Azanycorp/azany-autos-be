<?php

namespace App\Services;

use App\Enum\UserStatus;
use App\Http\Resources\LoginResource;
use App\Http\Resources\UserResource;
use App\Mail\PasswordResetCodeMail;
use App\Mail\TwoFACodeMail;
use App\Models\User;
use App\Models\Verify;
use App\Services\Auth\HttpService;
use App\Traits\HttpResponses;
use App\Traits\ShouldVerify;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AuthService
{
    use HttpResponses,ShouldVerify;

    public function __construct(
        private HttpService $httpService
    ) {}

    public function register($request)
    {
        $requestData = $request->only([
            'first_name',
            'last_name',
            'email',
            'country_id',
            'password',
        ]);

        $requestData['signed_up_from'] = 'Azanyautos';
        $requestData['type'] = 'azanyauto_buyer';

        $response = $this->httpService->register($requestData);
        if ($response->successful()) {

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
                'Registration successful. Kindly check your inbox for instructions on how to verify your account. Thanks.',
                new UserResource($user),
                Response::HTTP_CREATED
            );
        } else {
            return $this->errorResponse($response->json(), [], $response->status());
        }
    }

    public function login($request)
    {
        $credentials = $request->only(['email', 'password']);

        if (! Auth::attempt($credentials)) {
            return response()->json(['message' => 'Credentials do not match'], 401);
        }

        $response = $this->httpService->login($credentials);

        if ($response->successful()) {

            $user = User::where('email', $request->email)->first();

            if ($user->two_factor_enabled) {
                $verificationCode = mt_rand(1000, 9999);
                $expiry = now()->addMinutes(30);

                $user->update([
                    'verification_code' => $verificationCode,
                    'verification_code_expire_at' => $expiry,
                ]);

                Mail::to($user->email)->send(new TwoFACodeMail($user, $verificationCode));

                return $this->successResponse('2FA code sent. Please verify.', [
                    'email' => $user->email,
                    '2fa_required' => true,
                ], Response::HTTP_ACCEPTED);
            }

            return $this->issueToken($user, $request->email);

        } elseif ($response->status() === 401) {

            $user = User::where('email', $request->email)->first();

            if ($user) {

                $creationResponse = $this->httpService->register(
                    [
                        'first_name' => $user->first_name,
                        'last_name' => $user->last_name,
                        'email' => $user->email,
                        'country_id' => $user->country_id,
                        'password' => $request->password,
                        'signed_up_from' => 'Azanyautos',
                        'type' => 'auto_buyer',
                        'email_verified_at' => now(),
                        'is_verified' => true,
                        'status' => UserStatus::ACTIVE->value,
                    ]
                );

                if ($creationResponse->successful()) {

                    $reloginResponse = $this->httpService->login($credentials);

                    if ($reloginResponse->successful()) {
                        return $this->issueToken($user, $request->email);
                    } else {
                        return $this->errorResponse('Login failed, please try again.', [], $reloginResponse->status());
                    }
                } else {
                    return $this->errorResponse(
                        $creationResponse->json()['message'] ?? 'Failed to create account on auth service.',
                        [],
                        $creationResponse->status()
                    );
                }
            }

            return $this->errorResponse('Invalid credentials or user does not exist.', [], Response::HTTP_UNAUTHORIZED);
        } else {
            return $this->errorResponse($response->json()['message'], [], $response->status());
        }
    }

    public function verifyOtp($request)
    {
        $verify = Verify::with('user')
            ->where('token', $request->code)
            ->where('expires_at', '>', now())
            ->first();

        if (! $verify) {
            return $this->errorResponse('Invalid verification code.', [], Response::HTTP_BAD_REQUEST);
        }

        $user = $verify->user;
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

            return $this->successResponse('Account verified successfully.', $data, Response::HTTP_OK);
        } else {
            return $this->errorResponse($response->json()['message'], [], $response->status());
        }
    }

    public function resetPassword($request)
    {
        $user = User::where('email', $request->email)->first();

        if (! $user) {
            return $this->errorResponse('User not found', [], Response::HTTP_NOT_FOUND);
        }

        if (! method_exists($user, 'sendPasswordResetEmail')) {
            return $this->errorResponse('Unable to send email at this time.', [], Response::HTTP_BAD_REQUEST);
        }

        $result = $user->sendPasswordResetEmail();
        $message = is_string($result) ? $result : 'Reset email sent.';

        return $this->successResponse($message, []);
    }

    public function reset($request)
    {
        $user = User::where('email', $request->email)->first();

        if (! $user) {
            return $this->errorResponse('User not found', [], Response::HTTP_NOT_FOUND);
        }

        try {
            $token = decrypt($request->token);
        } catch (\Exception $e) {
            return $this->errorResponse('Invalid token', [], Response::HTTP_BAD_REQUEST);
        }

        $verify = Verify::where('email', $request->email)
            ->where('token', $token)
            ->first();

        if (! $verify) {
            return $this->errorResponse('Invalid or expired token', [], Response::HTTP_BAD_REQUEST);
        }

        $user->verify($verify, $request->password);

        return $this->successResponse('Updated successfully', []);
    }

    public function resendVerificationEmail($request)
    {
        $user = User::where('email', $request->email)->firstOrFail();

        if (! empty($user->email_verified_at)) {
            return $this->successResponse('Email already verified.', [], Response::HTTP_OK);
        }

        if (! method_exists($user, 'sendVerificationEmail')) {
            return $this->errorResponse('Unable to send verification email at this time.', [], Response::HTTP_BAD_REQUEST);
        }

        $result = $user->sendVerificationEmail();
        $message = is_string($result) ? $result : 'Verification email resent.';

        return $this->successResponse($message, [], Response::HTTP_OK);
    }

    public function profile()
    {
        return new UserResource(auth()->user());
    }

    public function updatePassword($request)
    {
        $authUser = userAuth();

        if (! Hash::check($request->old_password, $authUser->password)) {
            return $this->errorResponse('Old password is incorrect.', 400);
        }

        $authUser->update([
            'password' => bcrypt($request->password),
        ]);

        return $this->successResponse(null, 'Password updated');
    }

    public function verify2fa($request)
    {
        $user = User::where('verification_code', $request->verification_code)->first();

        if (! $user) {
            return $this->errorResponse('Invalide code entered, please try it again.', 403);
        }

        if ($user->verification_code_expire_at < now()) {
            return $this->errorResponse('error', ' Verification Code has Expired!', 404);
        }

        $user->update([
            'verification_code' => null,
            'verification_code_expire_at' => null,
        ]);

        return $this->issueToken($user, $user->email);
    }

    protected function issueToken($user)
    {

        $token = $user->createToken(
            $user->email,
            ['*']
        );

        return response()->json([
            'user' => new LoginResource($user),
            'token' => $token->plainTextToken,
        ]);
    }

    // forgot password section
    public function resendCode($request)
    {
        $email = Auth::check() ? Auth::user()->email : $request->email;

        $user = User::where('email', $email)->first();

        if (! $user) {
            return $this->errorResponse('Oops! No record found with your entry.', 404);
        }

        $verificationCode = mt_rand(1000, 9999);
        $expiry = now()->addMinutes(30);

        $user->update([
            'verification_code' => $verificationCode,
            'verification_code_expire_at' => $expiry,
        ]);

        $user = User::find($user->id);

        Mail::to($user->email)->send(new PasswordResetCodeMail($user, $verificationCode));

        return $this->successResponse('A new code has been sent to you');
    }

    public function verifyUserIdentity($request)
    {

        $user = User::where('email', $request->email)->first();
        if (! $user) {
            return $this->errorResponse('Oops! No record found with your entry.', 404);
        }
        $code = mt_rand(1000, 9999);
        $user->update([
            'verification_code' => $code,
            'verification_code_expire_at' => Carbon::now()->addMinutes(30),
        ]);

        Mail::to($user->email)->send(new PasswordResetCodeMail($user, $code));

        return $this->successResponse('A verification code has been sent to your email');
    }

    public function verifyCode($request)
    {
        $user = User::where('verification_code', $request->verification_code)->first();
        if (! $user) {
            return $this->errorResponse('Invalide code entered, please try it again.', 422);
        }

        if ($user->verification_code_expire_at < now()) {
            return $this->errorResponse('error', ' Verification Code has Expired!', 404);
        }
        $user->update([
            'verification_code' => null,
            'verification_code_expire_at' => null,
        ]);

        return $this->successResponse('Code Verified');
    }

    public function changePassword($request)
    {
        $user = User::where('email', $request->email)->firstOrFail();

        $user->update([
            'password' => bcrypt($request->password),
        ]);

        return $this->successResponse('Password Reset successfully!');
    }
}
