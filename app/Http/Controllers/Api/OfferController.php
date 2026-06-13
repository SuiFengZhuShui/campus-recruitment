<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Enterprise;
use App\Models\Job;
use App\Models\Offer;
use App\Models\Student;
use App\Notifications\OfferSent;
use Illuminate\Http\Request;

class OfferController extends Controller
{
    // === Enterprise: send offer ===

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
            'position' => 'required|string|max:200',
            'salary' => 'required|string|max:100',
            'start_date' => 'required|date',
            'note' => 'nullable|string|max:500',
        ]);

        $offer = Offer::create(array_merge($data, [
            'application_id' => $application->id,
            'status' => 'sent',
        ]));

        // Auto-set application status to accepted
        $application->fill(['status' => 'accepted'])->save();

        // Notify student
        try {
            $studentUser = $application->student->user;
            if ($studentUser) {
                $studentUser->notify(new OfferSent($offer));
            }
        } catch (\Exception $e) {
            \Log::warning('Failed to send offer notification: ' . $e->getMessage());
        }

        return response()->json([
            'code' => 200,
            'message' => '录用通知已发送',
            'data' => $offer,
        ]);
    }

    // === Student: respond to offer ===

    public function respond(Request $request, $id)
    {
        $user = auth()->user();
        if (!$user || !$user->isStudent()) {
            return response()->json(['code' => 401, 'message' => '请使用学生账号登录', 'data' => null], 401);
        }

        $student = Student::where('user_id', $user->id)->firstOrFail();

        // Only allow responding to own offers
        $offer = Offer::whereHas('application', function ($q) use ($student) {
            $q->where('student_id', $student->id);
        })->findOrFail($id);

        $data = $request->validate([
            'action' => 'required|in:accept,decline',
        ]);

        $newStatus = $data['action'] === 'accept' ? 'accepted' : 'declined';
        $offer->fill(['status' => $newStatus])->save();

        $label = $newStatus === 'accepted' ? '已接受录用通知' : '已拒绝录用通知';

        return response()->json([
            'code' => 200,
            'message' => $label,
            'data' => $offer,
        ]);
    }

    // === Student: my offers ===

    public function myOffers()
    {
        $user = auth()->user();
        if (!$user || !$user->isStudent()) {
            return response()->json(['code' => 401, 'message' => '请使用学生账号登录', 'data' => null], 401);
        }

        $student = Student::where('user_id', $user->id)->firstOrFail();

        $offers = Offer::with(['application.job:id,title,enterprise_id', 'application.job.enterprise:id,name'])
            ->whereHas('application', function ($q) use ($student) {
                $q->where('student_id', $student->id);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json([
            'code' => 200,
            'message' => 'success',
            'data' => [
                'list' => $offers->items(),
                'meta' => [
                    'current_page' => $offers->currentPage(),
                    'per_page' => $offers->perPage(),
                    'total' => $offers->total(),
                    'last_page' => $offers->lastPage(),
                ],
            ],
        ]);
    }
}
