<?php

namespace App\Http\API\V1\Controllers\Auth;

use App\Http\API\V1\Controllers\Admin\Controller;
use App\Http\API\V1\Repositories\Auth\AuthRepository;
use App\Http\API\V1\Requests\Auth\EmailLoginRequest;
use App\Http\API\V1\Requests\Auth\LogoutRequest;
use App\Http\API\V1\Requests\Auth\PhoneLoginRequest;
use App\Http\API\V1\Requests\Auth\RegisterDeviceRequest;
use App\Http\API\V1\Requests\Auth\RegisterRequest;
use App\Http\API\V1\Requests\Auth\UpdateRequest;
use App\Http\API\V1\Requests\Auth\VerifyOtpRequest;
use App\Http\Resources\Auth\LoginResource;
use App\Http\Resources\User\FullUserResource;
use Illuminate\Http\JsonResponse;

/**
 * @group User
 * APIs for User Management
 *
 * @subgroup Auth management
 *
 * @subgroupDescription APIs for login, register and all about auth
 */
class AuthController extends Controller
{
    public function __construct(
        protected AuthRepository $authRepository,
    ) {
        $this->middleware('auth:sanctum')->only(['update','profile','logout']);
    }

    /**
     * Phone Login
     *
     * This endpoint lets you log in with specific user
     *
     * @unauthenticated
     *
     * @responseFile storage/responses/auth/login/phone-login.json
     *
     * @return mixed
     */
    public function phoneLogin(PhoneLoginRequest $request): JsonResponse
    {
        $data = collect($request->validated());

        $authData = $this->authRepository->phoneLogin($data);

        return $this->showOne($authData, LoginResource::class);
    }

    /**
     * Email Login
     *
     * This endpoint lets you log in with specific user
     *
     * @unauthenticated
     *
     * @responseFile storage/responses/auth/login/email-login.json
     *
     * @return mixed
     */
    public function emailLogin(EmailLoginRequest $request): JsonResponse
    {
        $data = collect($request->validated());
        $authData = $this->authRepository->emailLogin($data);

        return $this->showOne($authData, LoginResource::class);
    }

    /**
     * login via firebase account
     *
     * This endpoint lets you login via firebase id token
     *
     * @unauthenticated
     *
     * @return mixed
     */
    // public function firebaseLogin(FirebaseLoginRequest $request)
    // {
    //     $data = collect($request->validated());
    //     $authData = $this->authRepository->firebaseLogin($data);
    //     return $this->showOne($authData, LoginResource::class);
    // }

    /**
     * Register
     *
     * This endpoint lets you add a new user
     *
     * @unauthenticated
     *
     * @responseFile storage/responses/auth/register.json
     *
     * @return mixed
     */
    public function register(RegisterRequest $request)
    {

        $userData = $request->validated();
        $user = $this->authRepository->register($userData);

        if ($user->phone) {
            $this->authRepository->sendPhoneOTP($user->phone);
        } elseif ($user->email) {
            $this->authRepository->sendEmailOTP($user->email);

        }

        return $this->showOne($user, FullUserResource::class, __('User registered successfully'));

    }

    /**
     * Validate phone
     *
     * This endpoint lets you verify phone using otp code
     *
     * @responseFile storage/responses/auth/verify_phone.json
     *
     * @return mixed
     */
    public function verifyOTP(VerifyOtpRequest $request)
    {
        $data = $request->validated();

        $verificationResult = $this->authRepository->verifyPhoneOTP(auth()->user()->phone, $data['otp_code']);

        auth()->user()->markPhoneAsVerified();
        return $this->responseMessage(__('Phone verified successfully'));
    }

    /**
     * Validate email
     *
     * This endpoint lets you verify email using otp code
     *
     * @responseFile storage/responses/auth/verify_email.json
     *
     * @return mixed
     */
    public function verifyEmailOTP(VerifyOtpRequest $request)
    {
        $data = $request->validated();

        $verificationResult = $this->authRepository->verifyEmailOTP(auth()->user()->email, $data['otp_code']);
        auth()->user()->markEmailAsVerified();
        return $this->responseMessage(__('Email verified successfully'));

    }

    /**
     * Logout
     *
     * This endpoint lets you log out
     *
     * @queryParam token string required User's token.
     * @queryParam udid string User's device udid.
     *
     * @responseFile storage/responses/auth/logout.json
     *
     * @return mixed
     */
    public function logout(LogoutRequest $request)
    {
        $data = collect($request->validated());
        $this->authRepository->logout($data);

        return $this->responseMessage(__('Successfully logged out'));
    }

    //
    //    /**
    //     * Refresh token
    //     *
    //     * This endpoint lets you refresh token to user
    //     *
    //     * @queryParam token string required User's token.
    //     * @responseFile storage/responses/auth/refresh.json
    //     *
    //     * @return mixed
    //     * @unauthenticated
    //     */
    //    public function refresh(RefreshRequest $request)
    //    {
    //        $data = collect($request->validated());
    //
    //        return $this->auth->refresh($data);
    //    }

    /**
     * Show user's profile
     *
     * This endpoint lets you show user's authenticated profile
     *
     * @responseFile storage/responses/auth/profile.json
     */
    public function profile(): JsonResponse
    {
        return $this->showOne($this->authRepository->profile(), FullUserResource::class);
    }

    //    /**
    //     * Update user
    //     *
    //     * This endpoint lets you update user's information
    //     *
    //     * @responseFile storage/responses/auth/update.json
    //     *
    //     * @param UpdateRequest $request
    //     * @return mixed
    //     */
    public function update(UpdateRequest $request)
    {
        $user = auth()->user();
        $user_data = $request->validated();
        $updatedUser = $this->authRepository->update($user, $user_data);

        return $this->showOne($updatedUser, FullUserResource::class, __('Your information updated successfully'));
    }

    /**
     * Request forget password
     *
     * This endpoint lets you update request forget password OTP
     *
     * @unauthenticated
     *
     * @responseFile storage/responses/auth/request_forget_password.json
     *
     * @return mixed
     */
    // public function requestForgetPassword(RequestForgetPasswordRequest $request)
    // {

    //     $data = collect($request->validated());

    //     return $this->authRepository->requestForgetPassword($data);
    // }

    /**
     * Forget password
     *
     * This endpoint lets you update user password with OTP verification
     *
     * @unauthenticated
     *
     * @responseFile storage/responses/auth/forget_password.json
     *
     * @return mixed
     */
    // public function forgetPassword(ForgetPasswordRequest $request)
    // {

    //     $data = collect($request->validated());
    //     return $this->authRepository->forgetPassword($data);
    // }
    //
    //    /**
    //     * Request email verification
    //     *
    //     * This endpoint lets you request email verification via OTP
    //     *
    //     * @unauthenticated
    //     * @responseFile storage/responses/auth/request_verify_email.json
    //     *
    //     * @return mixed
    //     */
    //    public function requestEmailVerification()
    //    {
    //        return $this->auth->requestEmailVerification();
    //    }
    //
    //    /**
    //     * Validate email
    //     *
    //     * This endpoint lets you verify email using otp code
    //     *
    //     * @responseFile storage/responses/auth/verify_email.json
    //     *
    //     * @param ValidateEmailRequest $request
    //     * @return mixed
    //     */
    //    public function verifyEmail(ValidateEmailRequest $request)
    //    {
    //        $data = collect($request->validated());

    //        return $this->authRepository->verifyEmail($data);
    //    }
    //
    //    /**
    //     * Change Password
    //     *
    //     * This endpoint lets you change the account password
    //     *
    //     * @responseFile storage/responses/auth/change_password.json
    //     *
    //     * @param ChangePasswordRequest $request
    //     * @return mixed
    //     */
    //    public function changePassword(ChangePasswordRequest $request)
    //    {
    //        $newPassword = $request->get('new_password');
    //
    //        return $this->auth->changePassword($newPassword);
    //    }
    //
    /**
     * Register User Device
     *
     * This endpoint lets you register User Device
     *
     * @responseFile storage/responses/auth/register_device.json
     */
    public function registerDevice(RegisterDeviceRequest $request)
    {
        $data = $request->validated();
        return $this->authRepository->registerDevice($data['udid'], $data['fcm_token']);
    }
}
