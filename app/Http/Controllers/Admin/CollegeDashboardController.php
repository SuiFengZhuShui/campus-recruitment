<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Enterprise;
use App\Models\Interview;
use App\Models\Job;
use App\Models\Offer;
use App\Models\Student;

class CollegeDashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $isCollegeScoped = $user->isCollege() && $user->college_id;

        if ($isCollegeScoped) {
            $collegeId = $user->college_id;
            $studentQuery = Student::where('college_id', $collegeId);
            $enterpriseQuery = Enterprise::where('college_id', $collegeId)->where('status', 'approved');
            $jobQuery = Job::whereHas('enterprise', function ($q) use ($collegeId) {
                $q->where('college_id', $collegeId);
            })->where('status', 'active');
            $appQuery = Application::whereHas('student', function ($q) use ($collegeId) {
                $q->where('college_id', $collegeId);
            });
            $interviewQuery = Interview::whereHas('application.student', function ($q) use ($collegeId) {
                $q->where('college_id', $collegeId);
            });
            $offerQuery = Offer::whereHas('application.student', function ($q) use ($collegeId) {
                $q->where('college_id', $collegeId);
            });
        } else {
            // school 管理员 或 super college 管理员：显示全部数据
            $studentQuery = Student::query();
            $enterpriseQuery = Enterprise::where('status', 'approved');
            $jobQuery = Job::where('status', 'active');
            $appQuery = Application::query();
            $interviewQuery = Interview::query();
            $offerQuery = Offer::query();
        }

        return view('admin.college.dashboard', [
            'isCollegeScoped' => $isCollegeScoped,
            'studentCount' => $studentQuery->count(),
            'enterpriseCount' => $enterpriseQuery->count(),
            'jobCount' => $jobQuery->count(),
            'applicationCount' => $appQuery->count(),
            'interviewCount' => $interviewQuery->count(),
            'offerCount' => $offerQuery->count(),
        ]);
    }

    public function students()
    {
        $user = auth()->user();
        $isCollegeScoped = $user->isCollege() && $user->college_id;

        if ($isCollegeScoped) {
            $students = Student::with('user:id,name,phone,email,status')
                ->where('college_id', $user->college_id)
                ->orderBy('created_at', 'desc')
                ->paginate(15);
        } else {
            $students = Student::with(['user:id,name,phone,email,status', 'college:id,name'])
                ->orderBy('created_at', 'desc')
                ->paginate(15);
        }

        return view('admin.college.students', compact('students', 'isCollegeScoped'));
    }

    public function enterprises()
    {
        $user = auth()->user();
        $isCollegeScoped = $user->isCollege() && $user->college_id;

        if ($isCollegeScoped) {
            $enterprises = Enterprise::with('user:id,name,phone,email')
                ->where('college_id', $user->college_id)
                ->orderBy('created_at', 'desc')
                ->paginate(15);
        } else {
            $enterprises = Enterprise::with(['user:id,name,phone,email', 'college:id,name'])
                ->orderBy('created_at', 'desc')
                ->paginate(15);
        }

        return view('admin.college.enterprises', compact('enterprises', 'isCollegeScoped'));
    }
}
