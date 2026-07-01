<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Enterprise;
use App\Models\Interview;
use App\Models\Job;
use App\Models\Student;
use App\Notifications\InterviewScheduled;
use Illuminate\Http\Request;

class InterviewController extends Controller
{
    // === Enterprise: create interview ===

    public function store(Request $request, $jobId, $applicationId)
    {
        $user = auth()->user();
        if (!$user || !$user->isEnterprise()) {
            return response()->json(['code' => 401, 'message' => '请使用企业账号登录', 'data' => null], 401);
        }

        $enterprise = Enterprise::where('user_id', $user->id)->firstOrFail();
        $job = Job::where('id', $jobId)->where('enterprise_id', $enterprise->id)->firstOrFail();
        $application = Application::where('id', $applicationId)->where('job_id', $job->id)->firstOrFail();

        $data = $request->validate([
            'scheduled_at' => 'required|date',
            'location' => 'required|string|max:200',
            'type' => 'required|in:online,on-site',
            'contact' => 'nullable|string|max:50',
            'note' => 'nullable|string|max:500',
        ]);

        $interview = Interview::create(array_merge($data, [
            'application_id' => $application->id,
        ]));

        // Auto-set application status to interviewed
        $application->fill(['status' => 'interviewed'])->save();

        // Notify student (non-blocking)
        try {
            $studentUser = $application->student->user;
            if ($studentUser) {
                $studentUser->notify(new InterviewScheduled($interview));
            }
        } catch (\Exception $e) {
            \Log::warning('Failed to send interview notification: ' . $e->getMessage());
        }

        return response()->json([
            'code' => 200,
            'message' => '面试邀请已发送',
            'data' => $interview,
        ]);
    }

    // === Student: respond to interview ===

    public function respond(Request $request, $id)
    {
        $user = auth()->user();
        if (!$user || !$user->isStudent()) {
            return response()->json(['code' => 401, 'message' => '请使用学生账号登录', 'data' => null], 401);
        }

        $student = Student::where('user_id', $user->id)->firstOrFail();

        // Load interview and verify it belongs to this student via application
        $interview = Interview::whereHas('application', function ($q) use ($student) {
            $q->where('student_id', $student->id);
        })->findOrFail($id);

        $data = $request->validate([
            'action' => 'required|in:accept,decline',
        ]);

        $newStatus = $data['action'] === 'accept' ? 'accepted' : 'declined';
        $interview->fill(['status' => $newStatus])->save();

        $label = $newStatus === 'accepted' ? '已接受面试邀请' : '已拒绝面试邀请';

        return response()->json([
            'code' => 200,
            'message' => $label,
            'data' => $interview,
        ]);
    }

    // === Student: my interviews ===

    public function myInterviews()
    {
        $user = auth()->user();
        if (!$user || !$user->isStudent()) {
            return response()->json(['code' => 401, 'message' => '请使用学生账号登录', 'data' => null], 401);
        }

        $student = Student::where('user_id', $user->id)->firstOrFail();

        $interviews = Interview::with(['application.job:id,title,enterprise_id', 'application.job.enterprise:id,name'])
            ->whereHas('application', function ($q) use ($student) {
                $q->where('student_id', $student->id);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json([
            'code' => 200,
            'message' => 'success',
            'data' => [
                'list' => $interviews->items(),
                'meta' => [
                    'current_page' => $interviews->currentPage(),
                    'per_page' => $interviews->perPage(),
                    'total' => $interviews->total(),
                    'last_page' => $interviews->lastPage(),
                ],
            ],
        ]);
    }
}
