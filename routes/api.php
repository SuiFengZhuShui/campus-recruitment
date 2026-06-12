<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CaptchaController;
use App\Http\Controllers\Api\JobController;
use App\Http\Controllers\Api\ApplicationController;
use App\Http\Controllers\Api\StudentController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['web']], function () {
    // Auth — public
    Route::get('auth/captcha', [CaptchaController::class, 'show']);
    Route::post('auth/login', [AuthController::class, 'login']);
    Route::post('auth/register/student', [AuthController::class, 'registerStudent']);
    Route::post('auth/register/enterprise', [AuthController::class, 'registerEnterprise']);

    // Auth — protected
    Route::middleware('auth')->group(function () {
        Route::get('auth/me', [AuthController::class, 'me']);
        Route::post('auth/logout', [AuthController::class, 'logout']);
    });

    // Student — protected
    Route::middleware('auth')->group(function () {
        Route::get('my/profile', [StudentController::class, 'profile']);
        Route::put('my/profile', [StudentController::class, 'updateProfile']);
        Route::post('my/resume', [StudentController::class, 'uploadResume']);
        Route::get('my/resume', [StudentController::class, 'downloadResume']);
        Route::get('resume/{studentId}', [StudentController::class, 'downloadResume']);
    });

    // Applications — student
    Route::middleware('auth')->group(function () {
        Route::post('jobs/{jobId}/apply', [ApplicationController::class, 'apply']);
        Route::get('my/applications', [ApplicationController::class, 'myApplications']);
        // Enterprise views applicants
        Route::get('my/jobs/{jobId}/applications', [ApplicationController::class, 'jobApplications']);
    });

    // Jobs — public
    Route::get('jobs', [JobController::class, 'index']);
    Route::get('jobs/cities', [JobController::class, 'cities']);
    Route::get('jobs/{id}', [JobController::class, 'show']);

    // Jobs — enterprise
    Route::middleware('auth')->group(function () {
        Route::get('my/jobs', [JobController::class, 'myJobs']);
        Route::post('my/jobs', [JobController::class, 'store']);
        Route::put('my/jobs/{id}', [JobController::class, 'update']);
        Route::delete('my/jobs/{id}', [JobController::class, 'destroy']);
        Route::post('my/jobs/{id}/toggle', [JobController::class, 'toggle']);
    });
});
