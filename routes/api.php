<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CaptchaController;
use Illuminate\Support\Facades\Route;

// Public
Route::get('auth/captcha', [CaptchaController::class, 'show']);
Route::post('auth/login', [AuthController::class, 'login']);
Route::post('auth/register/student', [AuthController::class, 'registerStudent']);
Route::post('auth/register/enterprise', [AuthController::class, 'registerEnterprise']);

// Protected
Route::middleware('auth')->group(function () {
    Route::get('auth/me', [AuthController::class, 'me']);
    Route::post('auth/logout', [AuthController::class, 'logout']);
});

