<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CaptchaController;
use App\Http\Controllers\Api\InterviewController;
use App\Http\Controllers\Api\JobController;
use App\Http\Controllers\Api\OfferController;
use App\Http\Controllers\Api\ApplicationController;
use App\Http\Controllers\Api\EnterpriseController as ApiEnterpriseController;
use App\Http\Controllers\Api\EnterpriseManageController;
use App\Http\Controllers\Api\StudentController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['web']], function () {
    // Auth — public
    Route::get('auth/captcha', [CaptchaController::class, 'show']);
    Route::post('auth/login', [AuthController::class, 'login']);
    Route::post('auth/register/student', [AuthController::class, 'registerStudent']);
    Route::post('auth/register/enterprise', [AuthController::class, 'registerEnterprise']);
    Route::post('auth/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('auth/reset-password', [AuthController::class, 'resetPassword']);

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
        Route::put('my/jobs/{jobId}/applications/{applicationId}/status', [ApplicationController::class, 'updateStatus']);

        // Interviews
        Route::post('my/jobs/{jobId}/applications/{applicationId}/interview', [InterviewController::class, 'store']);
        Route::put('my/interviews/{id}/respond', [InterviewController::class, 'respond']);
        Route::get('my/interviews', [InterviewController::class, 'myInterviews']);

        // Offers
        Route::post('my/jobs/{jobId}/applications/{applicationId}/offer', [OfferController::class, 'store']);
        Route::put('my/offers/{id}/respond', [OfferController::class, 'respond']);
        Route::get('my/offers', [OfferController::class, 'myOffers']);
    });

    // Jobs — public
    Route::get('jobs', [JobController::class, 'index']);
    Route::get('jobs/cities', [JobController::class, 'cities']);
    Route::get('jobs/{id}', [JobController::class, 'show']);

    // Enterprises — public
    Route::get('enterprises', [ApiEnterpriseController::class, 'index']);
    Route::get('enterprises/industries', [ApiEnterpriseController::class, 'industries']);
    Route::get('enterprises/{id}', [ApiEnterpriseController::class, 'show']);

    // Jobs — enterprise
    Route::middleware('auth')->group(function () {
        Route::get('my/jobs', [JobController::class, 'myJobs']);
        Route::post('my/jobs', [JobController::class, 'store']);
        Route::put('my/jobs/{id}', [JobController::class, 'update']);
        Route::delete('my/jobs/{id}', [JobController::class, 'destroy']);
        Route::post('my/jobs/{id}/toggle', [JobController::class, 'toggle']);

        // Enterprise self-service
        Route::get('my/enterprise', [EnterpriseManageController::class, 'profile']);
        Route::put('my/enterprise', [EnterpriseManageController::class, 'updateProfile']);
        Route::put('my/password', [EnterpriseManageController::class, 'updatePassword']);
        Route::post('my/docs', [EnterpriseManageController::class, 'uploadDoc']);
        Route::delete('my/docs/{id}', [EnterpriseManageController::class, 'deleteDoc']);
        Route::get('my/resumes', [EnterpriseManageController::class, 'resumes']);
    });
});
