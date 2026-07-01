<?php

use Illuminate\Support\Facades\Route;

// Web — frontend (authenticated school/college users redirect to admin)
Route::get('/', function () {
    if (auth()->check() && in_array(auth()->user()->role, ['school', 'college'])) {
        return redirect('/admin');
    }
    return view('frontend.home');
});
Route::get('/login', function () { return view('frontend.auth.login'); });
Route::get('/register', function () { return view('frontend.auth.register'); });
Route::get('/forgot-password', function () { return view('frontend.auth.forgot-password'); });
Route::get('/reset-password', function () { return view('frontend.auth.reset-password'); });
Route::get('/jobs', function () { return view('frontend.jobs.index'); });
Route::get('/jobs/{id}', function () { return view('frontend.jobs.show'); });
Route::get('/enterprises', function () { return view('frontend.enterprises.index'); });
Route::get('/enterprises/{id}', function () { return view('frontend.enterprises.show'); });

Route::middleware('auth')->group(function () {
    Route::get('/enterprise/dashboard', function () { return view('frontend.enterprise.dashboard'); });
    Route::get('/enterprise/docs/upload', function () { return view('frontend.enterprise.docs'); });
    Route::get('/enterprise/waiting', function () { return view('frontend.enterprise.waiting'); });
    Route::get('/enterprise/jobs', function () { return view('frontend.enterprise.jobs'); });
    Route::get('/enterprise/jobs/{id}/applications', function () { return view('frontend.enterprise.applicants'); });
    Route::get('/enterprise/resumes', function () { return view('frontend.enterprise.resumes'); });
    Route::get('/enterprise/profile', function () { return view('frontend.enterprise.profile'); });
    Route::get('/student/dashboard', function () { return view('frontend.student.dashboard'); });
    Route::get('/student/resume', function () { return view('frontend.student.resume'); });
    Route::get('/student/applications', function () { return view('frontend.student.applications'); });
    Route::get('/student/interviews', function () { return view('frontend.student.interviews'); });
    Route::get('/student/offers', function () { return view('frontend.student.offers'); });
    Route::get('/student/profile', function () { return redirect('/student/dashboard'); });
});

// Admin
Route::prefix('admin')->namespace('Admin')->group(function () {
    Route::post('logout', 'AuthController@logout')->name('admin.logout');

    // College admin routes (accessible by both school and college roles)
    Route::middleware(['auth', 'role:school,college'])->group(function () {
        Route::get('college', 'CollegeDashboardController@index')->name('admin.college');
        Route::get('college/students', 'CollegeDashboardController@students')->name('admin.college.students');
        Route::get('college/enterprises', 'CollegeDashboardController@enterprises')->name('admin.college.enterprises');

        // Enterprise review (school and college admins can approve/reject)
        Route::post('enterprises/{id}/approve', 'EnterpriseController@approve')->name('admin.enterprises.approve');
        Route::post('enterprises/{id}/reject', 'EnterpriseController@reject')->name('admin.enterprises.reject');
        Route::post('enterprises/{id}/docs/{docId}/approve', 'EnterpriseController@approveDoc')->name('admin.enterprises.doc.approve');
        Route::post('enterprises/{id}/docs/{docId}/reject', 'EnterpriseController@rejectDoc')->name('admin.enterprises.doc.reject');
        Route::post('enterprises/{id}/docs/approve-all', 'EnterpriseController@approveAllDocs')->name('admin.enterprises.doc.approveAll');
        Route::get('docs/{docId}/view', 'EnterpriseController@viewDoc')->name('admin.enterprises.doc.view');

        // Student ID Rules (school and college admins can manage)
        Route::get('rules', 'StudentIdRuleController@index')->name('admin.rules');
        Route::post('rules', 'StudentIdRuleController@store')->name('admin.rules.store');
        Route::put('rules/{id}', 'StudentIdRuleController@update')->name('admin.rules.update');
        Route::delete('rules/{id}', 'StudentIdRuleController@destroy')->name('admin.rules.destroy');
    });

    Route::middleware(['auth', 'role:school'])->group(function () {
        Route::get('/', 'DashboardController@index')->name('admin.dashboard');
        Route::get('enterprises', 'EnterpriseController@index')->name('admin.enterprises');
        Route::get('enterprises/{id}', 'EnterpriseController@show')->name('admin.enterprises.show');
        Route::get('enterprises/{id}/edit', 'EnterpriseController@edit')->name('admin.enterprises.edit');
        Route::put('enterprises/{id}', 'EnterpriseController@update')->name('admin.enterprises.update');
        Route::delete('enterprises/{id}', 'EnterpriseController@destroy')->name('admin.enterprises.destroy');

        // Colleges
        Route::get('colleges', 'CollegeController@index')->name('admin.colleges');
        Route::post('colleges', 'CollegeController@store')->name('admin.colleges.store');
        Route::put('colleges/{id}', 'CollegeController@update')->name('admin.colleges.update');
        Route::delete('colleges/{id}', 'CollegeController@destroy')->name('admin.colleges.destroy');

        // Schools
        Route::get('schools', 'SchoolController@index')->name('admin.schools');
        Route::post('schools', 'SchoolController@store')->name('admin.schools.store');
        Route::put('schools/{id}', 'SchoolController@update')->name('admin.schools.update');
        Route::delete('schools/{id}', 'SchoolController@destroy')->name('admin.schools.destroy');

        // Users
        Route::get('users', 'UserController@index')->name('admin.users');
        Route::get('users/{id}/edit', 'UserController@edit')->name('admin.users.edit');
        Route::post('users/{id}', 'UserController@update')->name('admin.users.update');
        Route::post('users/{id}/toggle', 'UserController@toggle')->name('admin.users.toggle');

        // College Admins
        Route::get('admins', 'CollegeAdminController@index')->name('admin.admins');
        Route::get('admins/create', 'CollegeAdminController@create')->name('admin.admins.create');
        Route::post('admins', 'CollegeAdminController@store')->name('admin.admins.store');
        Route::get('admins/{id}/edit', 'CollegeAdminController@edit')->name('admin.admins.edit');
        Route::put('admins/{id}', 'CollegeAdminController@update')->name('admin.admins.update');
        Route::delete('admins/{id}', 'CollegeAdminController@destroy')->name('admin.admins.destroy');
        Route::post('admins/{id}/toggle', 'CollegeAdminController@toggle')->name('admin.admins.toggle');

        // Jobs
        Route::get('jobs', 'JobController@index')->name('admin.jobs');
        Route::post('jobs/{id}/toggle', 'JobController@toggle')->name('admin.jobs.toggle');
        Route::delete('jobs/{id}', 'JobController@destroy')->name('admin.jobs.destroy');
    });

    // Profile — both school and college can change password
    Route::middleware(['auth', 'role:school,college'])->group(function () {
        Route::get('profile', 'ProfileController@index')->name('admin.profile');
        Route::put('profile/password', 'ProfileController@updatePassword')->name('admin.profile.password');
    });
});
