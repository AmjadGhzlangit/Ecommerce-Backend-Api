<?php

namespace App\Http\API\V1\Repositories\Auth;

use App\Mail\OTP as OtpMail;
use App\Mail\PasswordResetOTPMail;
use App\Models\User;
use App\Traits\ApiResponse;
use GuzzleHttp\Promise\Create;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\NewAccessToken;
use Tzsk\Otp\Facades\Otp;

class AuthRepository
{
    use ApiResponse;

    protected User $model;

    public function __construct(User $model)
    {
        $this->model = $model;
    }

    public function register($data): User
    {
        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);
        if (isset($data['udid'])) {
            $udid = $data['udid'];
            $this->registerDevice($udid, $data['fcm_token'], $user);
        }
        return $user;
    }

    public function profile(): User
    {
        return auth()->user();
    }

    /**
     * @throws ValidationException
     */
    public function phoneLogin($data): array
    {
        $user = null;
        if ($data['phone']) {
            $user = User::where('phone', $data->get('phone'))->first();
        }
        if (!$user || !Hash::check($data->get('password'), $user->password)) {
            throw ValidationException::withMessages([
                'phone' => ['The provided credentials are incorrect.'],
            ]);
        }
        $udid = $data->get('udid');
        if (!is_null($udid)) {
            $this->registerDevice($udid, $data->get('fcm_token'), $user);
        }
        $accessToken = $user->createAuthToken();
        return $this->respondWithToken($accessToken, $user);
    }

    /**
     * @throws ValidationException
     */
    public function emailLogin($data): array
    {
        $user = null;
        if ($data['email']) {
            $user = User::where('email', $data->get('email'))->first();
        }
        if (!$user || !Hash::check($data->get('password'), $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }
        $udid = $data->get('udid');
        if (!is_null($udid)) {
            // Register the device only if udid exists
            $this->registerDevice($udid, $data->get('fcm_token'), $user);
        }
        $accessToken = $user->createAuthToken();
        return $this->respondWithToken($accessToken, $user);
    }

    public function logout($data): JsonResponse
    {
        $udid = $data->get('udid');
        if (auth()->check()) {
            if (!is_null($udid)) {
                $this->unregisterDevice($udid);
                auth()->user()->tokens()->delete();
                return $this->responseMessage(__('Successfully logged out'));
            } else {
                auth()->user()->tokens()->delete();
                return $this->responseMessage(__('Successfully logged out'));
            }
        } else {
            return $this->responseMessage(__('User is not authenticated'), 401);
        }

    }

    public function update(User|Model $user, $data): User
    {

        if (Arr::exists($data, 'password')) {
            $data['password'] = Hash::make($data['password']);
        }
        $user->fill($data);
        $user->save();

        return $user;

    }

    public function registerDevice($udid = null, $fcm_token = null, $user = null): JsonResponse
    {

        if (is_null($user)) {
            $user = auth()->user();
        }

        $user->devices()->updateOrCreate(
            ['udid' => $udid],
            ['fcm_token' => $fcm_token]);

        return $this->responseMessage(__('Device is registered successfully'));
    }

    public function unregisterDevice($udid = null, $user = null)
    {
        if (is_null($user)) {
            $user = auth()->user();
        }
        if (!is_null($udid)) {
            $user->devices()->where('udid', $udid)->delete();
        }
    }

    public function sendPhoneOTP($phone): JsonResponse
    {
        $key = $phone . config('app.key');
        $code = Otp::generate($key);
        // TODO: Implement logic to send OTP via SMS to the provided phone number
        return $this->responseMessage(__('Code is sent successfully'));
    }

    public function verifyPhoneOTP($phone, $code)
    {
        if (app()->environment('testing') || app()->environment('local')) {
            $fixedOtp = '123456';
            $testKey = $phone . config('test.key');
            $code === $fixedOtp;
            $result = Otp::match($code, $testKey);
            if (!$result) {
                throw ValidationException::withMessages([
                    'message' => ['Invalid OTP code'],
                ]);
            }
            return $result;
        }

        $key = $phone . config('app.key');
        $result = Otp::match($code, $key);
        if (!$result) {
            throw ValidationException::withMessages([
                'message' => ['Invalid OTP code'],
            ]);
        }
        return $result;

    }

    public function sendEmailOTP($email): JsonResponse
    {
        $key = $email . config('app.key');
        $code = Otp::generate($key);
        Mail::to($email)->send(new OtpMail($code));

        return $this->responseMessage(__('Code is sent successfully'));
    }

    public function verifyEmailOTP($email, $code)
    {
        if (app()->environment('testing') || app()->environment('local')) {
            $fixedOtp = '123456';
            $testKey = $email . config('test.key');
            $code === $fixedOtp;

            $result = Otp::match($code, $testKey);
            if (!$result) {
                throw ValidationException::withMessages([
                    'message' => ['Invalid OTP code'],
                ]);
            }
            return $result;

        }
        $key = $email . config('app.key');

        $result = Otp::match($code, $key);
        if (!$result) {
            throw ValidationException::withMessages([
                'message' => ['Invalid OTP code'],
            ]);
        }
        return $result;
    }

    public function firebaseLogin($data): array
    {
        $auth = app('firebase.auth');
        try {
            $verifiedIdToken = $auth->verifyIdToken($data->get('id_token'));
        } catch (InvalidToken $e) {
            return $this->responseMessage(__('The token is invalid'), 401);
        } catch (\InvalidArgumentException $e) {
            return $this->responseMessage(__('The token could not be parsed'), 401);
        }

        $uid = $verifiedIdToken->claims()->get('sub');
        $user = User::where('firebase_uid', $uid)->first();
        $firebaseUser = $auth->getUser($uid);
        if (is_null($user)) {
            $phone = $firebaseUser->phoneNumber;

            if (is_null($phone)) {
                $phone = $data->get('phone');
                if (is_null($phone)) {
                    return $this->responseMessage(__('phone is required'), 401);
                }
            }
            //check if user has registered before

            $user = User::where('phone', $phone)->first();

            if (is_null($user)) {
                //if user does not exist then create it
                $user = User::create([
                    'first_name' => $firebaseUser->first_name,
                    'last_name' => $firebaseUser->last_name,
                    'email' => $firebaseUser->email,
                    'password' => Hash::make(Str::random(15)),
                    'phone' => $phone,
                ]);

            }

            $user->firebase_uid = $firebaseUser->uid;

        }

        if ($firebaseUser->emailVerified) {
            $user->markEmailAsVerified();

        }

        if ($firebaseUser->phoneNumber) {
            $user->markPhoneAsVerified();
        }
        $udid = $data->get('udid');
        if (!is_null($udid)) {
            $this->registerDevice($udid, $data->get('fcm_token'), $user);
        }
        $accessToken = $user->createAuthToken();
        $refreshToken = $user->createRefreshToken();
        return $this->respondWithToken($accessToken, $user);
    }

    public function requestForgetPassword($data)
    {
        if ($data['email']) {
            $user = User::where('email', $data['email'])->first();

            $otpCode = Otp::generate($data['email'] . config('app.key'));
            Mail::to($user->email)->send(new PasswordResetOTPMail($otpCode));
        } elseif ($data['phone']) {
            $user = User::where('phone', $data['phone'])->first();

            $key = $data['phone'] . config('app.key');
            $code = Otp::generate($key);
            // Implement logic to send OTP via SMS to the provided phone number
        }

        return $this->responseMessage(__('Password reset Code sent successfully'));
    }

    public function forgetPassword($data)
    {
        $user = null;
        $code = null;
        if ($data['email']) {
            $user = User::where('email', $data['email'])->first();
            $code = $this->verifyEmailOTP($user->email, $data['otp_code']);
        } elseif ($data['phone']) {
            $user = User::where('phone', $data['phone'])->first();
            $code = $this->verifyPhoneOTP($user->phone, $data['otp_code']);
        }

        if (!$user || !$code) {
            throw ValidationException::withMessages([
                'message' => ['The provided credentials are incorrect.'],
            ]);
        }
        $userUpdated = $this->update($user, $data->toArray());

        return $this->responseMessage(__('Password reset successful'));
    }

    protected function respondWithToken(NewAccessToken $token, $user = null): array
    {
        return [
            'token_type' => 'bearer',
            'access_token' => $token->plainTextToken,
            'access_expires_at' => $token->accessToken->expiresIn,
            'user' => $user,
        ];
    }
}
