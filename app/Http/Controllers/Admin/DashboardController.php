<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Enterprise;
use App\Models\Job;
use App\Models\Student;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard', [
            'enterprisePending' => Enterprise::where('status', 'pending')->count(),
            'enterpriseApproved' => Enterprise::where('status', 'approved')->count(),
            'enterpriseRejected' => Enterprise::where('status', 'rejected')->count(),
            'studentCount' => Student::count(),
            'jobActive' => Job::where('status', 'active')->count(),
            'jobInactive' => Job::where('status', 'inactive')->count(),
            'applicationCount' => Application::count(),
            'userCount' => User::count(),
            'activeUserCount' => User::where('status', 'active')->count(),
            'disabledUserCount' => User::where('status', 'disabled')->count(),
        ]);
    }
}
