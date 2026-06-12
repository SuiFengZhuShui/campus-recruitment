<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Enterprise;
use App\Models\Job;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class JobController extends Controller
{
    // === Public ===

    public function index(Request $request)
    {
        $query = Job::with('enterprise:id,name,industry,scale')->where('status', 'active');

        if ($request->keyword) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->keyword . '%')
                  ->orWhere('skills', 'like', '%' . $request->keyword . '%');
            });
        }
        if ($request->city) {
            $query->where('city', $request->city);
        }
        if ($request->type) {
            $query->where('type', $request->type);
        }
        if ($request->education) {
            $query->where('education', $request->education);
        }
        if ($request->salary_min) {
            $query->where('salary_max', '>=', $request->salary_min);
        }

        // Filter by industry via enterprise
        if ($request->industry) {
            $query->whereHas('enterprise', function ($q) use ($request) {
                $q->where('industry', $request->industry);
            });
        }

        $jobs = $query->orderBy('created_at', 'desc')->paginate(15);

        return response()->json([
            'code' => 200,
            'message' => 'success',
            'data' => [
                'list' => $jobs->items(),
                'meta' => [
                    'current_page' => $jobs->currentPage(),
                    'per_page' => $jobs->perPage(),
                    'total' => $jobs->total(),
                    'last_page' => $jobs->lastPage(),
                ],
            ],
        ]);
    }

    public function show($id)
    {
        $job = Job::with('enterprise:id,name,industry,scale,intro')->where('status', 'active')->findOrFail($id);

        return response()->json([
            'code' => 200,
            'message' => 'success',
            'data' => $job,
        ]);
    }

    public function cities()
    {
        $cities = Job::where('status', 'active')->distinct()->pluck('city');

        return response()->json(['code' => 200, 'message' => 'success', 'data' => $cities]);
    }

    // === Enterprise ===

    public function myJobs(Request $request)
    {
        $enterprise = $this->getApprovedEnterprise();

        $jobs = Job::where('enterprise_id', $enterprise->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json([
            'code' => 200,
            'message' => 'success',
            'data' => [
                'list' => $jobs->items(),
                'meta' => [
                    'current_page' => $jobs->currentPage(),
                    'per_page' => $jobs->perPage(),
                    'total' => $jobs->total(),
                    'last_page' => $jobs->lastPage(),
                ],
            ],
        ]);
    }

    public function store(Request $request)
    {
        $enterprise = $this->getApprovedEnterprise();

        $data = $request->validate([
            'title' => 'required|string|max:200',
            'count' => 'required|integer|min:1',
            'city' => 'required|string|max:50',
            'salary_min' => 'required|integer|min:0',
            'salary_max' => 'required|integer|gte:salary_min',
            'education' => 'required|string|max:30',
            'major' => 'nullable|string|max:200',
            'skills' => 'nullable|string|max:500',
            'type' => 'required|in:full-time,internship',
            'duty' => 'required|string',
            'requirement' => 'required|string',
            'welfare' => 'nullable|string',
        ]);

        $job = Job::create(array_merge($data, [
            'enterprise_id' => $enterprise->id,
            'status' => 'active',
        ]));

        return response()->json(['code' => 200, 'message' => '发布成功', 'data' => $job]);
    }

    public function update(Request $request, $id)
    {
        $enterprise = $this->getApprovedEnterprise();

        $job = Job::where('id', $id)->where('enterprise_id', $enterprise->id)->firstOrFail();

        $data = $request->validate([
            'title' => 'string|max:200',
            'count' => 'integer|min:1',
            'city' => 'string|max:50',
            'salary_min' => 'integer|min:0',
            'salary_max' => 'integer|gte:salary_min',
            'education' => 'string|max:30',
            'major' => 'nullable|string|max:200',
            'skills' => 'nullable|string|max:500',
            'type' => 'in:full-time,internship',
            'duty' => 'string',
            'requirement' => 'string',
            'welfare' => 'nullable|string',
        ]);

        $job->fill($data)->save();

        return response()->json(['code' => 200, 'message' => '更新成功', 'data' => $job]);
    }

    public function destroy($id)
    {
        $enterprise = $this->getApprovedEnterprise();

        $job = Job::where('id', $id)->where('enterprise_id', $enterprise->id)->firstOrFail();
        $job->delete();

        return response()->json(['code' => 200, 'message' => '已删除', 'data' => null]);
    }

    public function toggle($id)
    {
        $enterprise = $this->getApprovedEnterprise();

        $job = Job::where('id', $id)->where('enterprise_id', $enterprise->id)->firstOrFail();
        $job->update(['status' => $job->status === 'active' ? 'inactive' : 'active']);

        return response()->json(['code' => 200, 'message' => $job->status === 'active' ? '已上架' : '已下架', 'data' => $job]);
    }

    // === Helper ===

    private function getApprovedEnterprise(): Enterprise
    {
        $user = auth()->user();
        if (!$user || !$user->isEnterprise()) {
            abort(401, '请使用企业账号登录');
        }

        $enterprise = Enterprise::where('user_id', $user->id)->first();
        if (!$enterprise || $enterprise->status !== 'approved') {
            abort(403, '企业未通过审核');
        }

        return $enterprise;
    }
}
