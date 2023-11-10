<?php

use App\Http\API\V1\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'auth'], function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('verify-otp', [AuthController::class, 'verifyOTP']);
    Route::post('verify-otp/email', [AuthController::class, 'verifyEmailOTP']);
    Route::put('update', [AuthController::class, 'update']);
    Route::get('profile', [AuthController::class, 'profile']);
    Route::post('phone-login', [AuthController::class, 'phoneLogin']);
    Route::post('email-login', [AuthController::class, 'emailLogin']);
    Route::post('logout', [AuthController::class, 'logout']);

    Route::post('firebase-login', [AuthController::class, 'firebaseLogin']);
    Route::post('request-forget-password', [AuthController::class,'requestForgetPassword']);
    Route::post('forget-password', [AuthController::class,'forgetPassword']);

    Route::post('register-device', [AuthController::class, 'registerDevice']);

});