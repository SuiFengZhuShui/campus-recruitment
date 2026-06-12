<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Enterprise;
use App\Models\Job;
use App\Models\Student;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    // === Student ===

    public function apply($jobId)
    {
        $user = auth()->user();
        if (!$user || !$user->isStudent()) {
            return response()->json(['code' => 401, 'message' => '请使用学生账号登录', 'data' => null], 401);
        }

        $job = Job::where('status', 'active')->findOrFail($jobId);
        $student = Student::where('user_id', $user->id)->firstOrFail();

        // Check duplicate
        $exists = Application::where('job_id', $jobId)->where('student_id', $student->id)->exists();
        if ($exists) {
            return response()->json(['code' => 422, 'message' => '已投递过该岗位', 'data' => null], 422);
        }

        $app = Application::create([
            'job_id' => $jobId,
            'student_id' => $student->id,
        ]);

        return response()->json(['code' => 200, 'message' => '投递成功', 'data' => $app]);
    }

    public function myApplications()
    {
        $user = auth()->user();
        if (!$user || !$user->isStudent()) {
            return response()->json(['code' => 401, 'message' => '请使用学生账号登录', 'data' => null], 401);
        }

        $student = Student::where('user_id', $user->id)->firstOrFail();

        $applications = Application::with('job:id,title,city,salary_min,salary_max,education,type,enterprise_id')
            ->where('student_id', $student->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Eager load enterprise name for each job
        $applications->load('job.enterprise:id,name');

        return response()->json([
            'code' => 200,
            'message' => 'success',
            'data' => [
                'list' => $applications->items(),
                'meta' => [
                    'current_page' => $applications->currentPage(),
                    'per_page' => $applications->perPage(),
                    'total' => $applications->total(),
                    'last_page' => $applications->lastPage(),
                ],
            ],
        ]);
    }

    // === Enterprise ===

    public function jobApplications($jobId)
    {
        $user = auth()->user();
        if (!$user || !$user->isEnterprise()) {
            return response()->json(['code' => 401, 'message' => '请使用企业账号登录', 'data' => null], 401);
        }

        $enterprise = Enterprise::where('user_id', $user->id)->firstOrFail();
        $job = Job::where('id', $jobId)->where('enterprise_id', $enterprise->id)->firstOrFail();

        $applications = Application::with('student.user:id,name,phone,email')
            ->where('job_id', $job->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json([
            'code' => 200,
            'message' => 'success',
            'data' => [
                'job' => $job->only(['id', 'title']),
                'list' => $applications->items(),
                'meta' => [
                    'current_page' => $applications->currentPage(),
                    'per_page' => $applications->perPage(),
                    'total' => $applications->total(),
                    'last_page' => $applications->lastPage(),
                ],
            ],
        ]);
    }
}
