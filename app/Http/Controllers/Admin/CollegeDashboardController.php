<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Enterprise;
use App\Models\Job;
use App\Models\Student;

class CollegeDashboardController extends Controller
{
    public function index()
    {
        $collegeId = auth()->user()->college_id;

        if (!$collegeId) {
            return view('admin.college.dashboard', [
                'studentCount' => 0,
                'enterpriseCount' => 0,
                'jobCount' => 0,
                'applicationCount' => 0,
                'interviewCount' => 0,
                'offerCount' => 0,
            ]);
        }

        $applicationCount = Application::whereHas('student', function ($q) use ($collegeId) {
            $q->where('college_id', $collegeId);
        })->count();

        $interviewCount = \App\Models\Interview::whereHas('application.student', function ($q) use ($collegeId) {
            $q->where('college_id', $collegeId);
        })->count();

        $offerCount = \App\Models\Offer::whereHas('application.student', function ($q) use ($collegeId) {
            $q->where('college_id', $collegeId);
        })->count();

        return view('admin.college.dashboard', [
            'studentCount' => Student::where('college_id', $collegeId)->count(),
            'enterpriseCount' => Enterprise::where('college_id', $collegeId)->where('status', 'approved')->count(),
            'jobCount' => Job::whereHas('enterprise', function ($q) use ($collegeId) {
                $q->where('college_id', $collegeId);
            })->where('status', 'active')->count(),
            'applicationCount' => $applicationCount,
            'interviewCount' => $interviewCount,
            'offerCount' => $offerCount,
        ]);
    }

    public function students()
    {
        $collegeId = auth()->user()->college_id;

        $students = collect();
        if ($collegeId) {
            $students = Student::with('user:id,name,phone,email,status')
                ->where('college_id', $collegeId)
                ->orderBy('created_at', 'desc')
                ->paginate(15);
        }

        return view('admin.college.students', compact('students'));
    }

    public function enterprises()
    {
        $collegeId = auth()->user()->college_id;

        $enterprises = collect();
        if ($collegeId) {
            $enterprises = Enterprise::with('user:id,name,phone,email')
                ->where('college_id', $collegeId)
                ->orderBy('created_at', 'desc')
                ->paginate(15);
        }

        return view('admin.college.enterprises', compact('enterprises'));
    }
}
