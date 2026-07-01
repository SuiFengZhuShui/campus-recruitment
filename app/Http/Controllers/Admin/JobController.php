<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Job;
use Illuminate\Http\Request;

class JobController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'all');
        $search = $request->query('search');

        $query = Job::with('enterprise:id,name')
            ->orderBy('created_at', 'desc');

        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%")
                  ->orWhere('major', 'like', "%{$search}%")
                  ->orWhereHas('enterprise', function ($eq) use ($search) {
                      $eq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $jobs = $query->paginate(15);

        if ($status !== 'all') {
            $jobs->appends(['status' => $status]);
        }
        if ($search) {
            $jobs->appends(['search' => $search]);
        }

        return view('admin.jobs.index', compact('jobs', 'status', 'search'));
    }

    public function toggle($id)
    {
        $job = Job::findOrFail($id);
        $newStatus = $job->status === 'active' ? 'closed' : 'active';
        $job->fill(['status' => $newStatus])->save();

        $label = $newStatus === 'active' ? '已上架' : '已下架';
        return back()->with('success', '「' . $job->title . '」' . $label);
    }

    public function destroy($id)
    {
        $job = Job::findOrFail($id);
        $title = $job->title;
        $job->delete();

        return back()->with('success', '已删除「' . $title . '」');
    }
}
