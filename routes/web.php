<?php

use Illuminate\Support\Facades\Route;

// Web — frontend
Route::get('/', function () { return view('frontend.home'); });
Route::get('/login', function () { return view('frontend.auth.login'); });
Route::get('/register', function () { return view('frontend.auth.register'); });
Route::get('/forgot-password', function () { return view('frontend.auth.forgot-password'); });
Route::get('/reset-password', function () { return view('frontend.auth.reset-password'); });
Route::get('/jobs/{id}', function () { return view('frontend.jobs.show'); });

Route::middleware('auth')->group(function () {
    Route::get('/enterprise/waiting', function () { return view('frontend.enterprise.waiting'); });
    Route::get('/enterprise/jobs', function () { return view('frontend.enterprise.jobs'); });
    Route::get('/enterprise/jobs/{id}/applications', function () { return view('frontend.enterprise.applicants'); });
    Route::get('/student/applications', function () { return view('frontend.student.applications'); });
    Route::get('/student/interviews', function () { return view('frontend.student.interviews'); });
    Route::get('/student/offers', function () { return view('frontend.student.offers'); });
    Route::get('/student/profile', function () { return view('frontend.student.profile'); });
});

// Admin
Route::prefix('admin')->namespace('Admin')->group(function () {
    Route::get('login', 'AuthController@showLoginForm')->name('admin.login');
    Route::post('login', 'AuthController@login');
    Route::post('logout', 'AuthController@logout')->name('admin.logout');

    Route::middleware(['auth', 'role:school'])->group(function () {
        Route::get('/', 'DashboardController@index')->name('admin.dashboard');
        Route::get('enterprises', 'EnterpriseController@index')->name('admin.enterprises');
        Route::get('enterprises/{id}', 'EnterpriseController@show')->name('admin.enterprises.show');
        Route::post('enterprises/{id}/approve', 'EnterpriseController@approve')->name('admin.enterprises.approve');
        Route::post('enterprises/{id}/reject', 'EnterpriseController@reject')->name('admin.enterprises.reject');
        Route::post('enterprises/{id}/docs/{docId}/approve', 'EnterpriseController@approveDoc')->name('admin.enterprises.doc.approve');
        Route::post('enterprises/{id}/docs/{docId}/reject', 'EnterpriseController@rejectDoc')->name('admin.enterprises.doc.reject');
        Route::post('enterprises/{id}/docs/approve-all', 'EnterpriseController@approveAllDocs')->name('admin.enterprises.doc.approveAll');

        // Colleges
        Route::get('colleges', 'CollegeController@index')->name('admin.colleges');
        Route::post('colleges', 'CollegeController@store')->name('admin.colleges.store');
        Route::put('colleges/{id}', 'CollegeController@update')->name('admin.colleges.update');
        Route::delete('colleges/{id}', 'CollegeController@destroy')->name('admin.colleges.destroy');

        // Student ID Rules
        Route::get('rules', 'StudentIdRuleController@index')->name('admin.rules');
        Route::post('rules', 'StudentIdRuleController@store')->name('admin.rules.store');
        Route::put('rules/{id}', 'StudentIdRuleController@update')->name('admin.rules.update');
        Route::delete('rules/{id}', 'StudentIdRuleController@destroy')->name('admin.rules.destroy');

        // Schools
        Route::get('schools', 'SchoolController@index')->name('admin.schools');
        Route::post('schools', 'SchoolController@store')->name('admin.schools.store');
        Route::put('schools/{id}', 'SchoolController@update')->name('admin.schools.update');
        Route::delete('schools/{id}', 'SchoolController@destroy')->name('admin.schools.destroy');

        // Users
        Route::get('users', 'UserController@index')->name('admin.users');
        Route::post('users/{id}/toggle', 'UserController@toggle')->name('admin.users.toggle');
    });
});
