<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\College;
use App\Models\Enterprise;
use App\Models\EnterpriseDoc;
use App\Notifications\EnterpriseApproved;
use App\Notifications\EnterpriseRejected;
use Illuminate\Http\Request;

class EnterpriseController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'all');
        $search = $request->query('search');

        $query = Enterprise::with('user', 'docs', 'college')
            ->orderBy('created_at', 'desc');

        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('contact_name', 'like', "%{$search}%")
                  ->orWhere('contact_phone', 'like', "%{$search}%")
                  ->orWhere('industry', 'like', "%{$search}%");
            });
        }

        $enterprises = $query->paginate(15);

        if ($status !== 'all') {
            $enterprises->appends(['status' => $status]);
        }
        if ($search) {
            $enterprises->appends(['search' => $search]);
        }

        return view('admin.enterprises.index', compact('enterprises', 'status', 'search'));
    }

    public function show($id)
    {
        $enterprise = Enterprise::with('user', 'docs', 'college')->findOrFail($id);

        return view('admin.enterprises.show', compact('enterprise'));
    }

    public function approve(Request $request, $id)
    {
        $enterprise = Enterprise::findOrFail($id);

        if ($enterprise->status !== 'pending') {
            return back()->with('error', '只能审核待审企业');
        }

        $data = $request->validate([
            'college_id' => 'required|exists:colleges,id',
        ]);

        $enterprise->update([
            'status' => 'approved',
            'college_id' => $data['college_id'],
        ]);

        // Notify enterprise user (non-blocking)
        try {
            if ($enterprise->user) {
                $enterprise->user->notify(new EnterpriseApproved($enterprise));
            }
        } catch (\Exception $e) {
            \Log::warning('Failed to send approval notification: ' . $e->getMessage());
        }

        return redirect()->route('admin.enterprises', ['status' => 'approved'])
            ->with('success', '已通过「' . $enterprise->name . '」的审核');
    }

    public function reject(Request $request, $id)
    {
        $enterprise = Enterprise::findOrFail($id);

        if ($enterprise->status !== 'pending') {
            return back()->with('error', '只能审核待审企业');
        }

        $data = $request->validate([
            'audit_remark' => 'required|string|max:500',
        ]);

        $enterprise->update([
            'status' => 'rejected',
            'audit_remark' => $data['audit_remark'],
        ]);

        // Notify enterprise user (non-blocking)
        try {
            if ($enterprise->user) {
                $enterprise->user->notify(new EnterpriseRejected($enterprise));
            }
        } catch (\Exception $e) {
            \Log::warning('Failed to send rejection notification: ' . $e->getMessage());
        }

        return redirect()->route('admin.enterprises', ['status' => 'rejected'])
            ->with('success', '已驳回「' . $enterprise->name . '」的申请');
    }

    public function approveDoc(Request $request, $enterpriseId, $docId)
    {
        $doc = EnterpriseDoc::where('id', $docId)->where('enterprise_id', $enterpriseId)->firstOrFail();
        $doc->update(['status' => 'approved']);

        return back()->with('success', '已通过「' . ($doc->type === 'license' ? '营业执照' : ($doc->type === 'id_card' ? '身份证' : '授权书')) . '」');
    }

    public function rejectDoc(Request $request, $enterpriseId, $docId)
    {
        $doc = EnterpriseDoc::where('id', $docId)->where('enterprise_id', $enterpriseId)->firstOrFail();

        $data = $request->validate([
            'reject_reason' => 'required|string|max:500',
        ]);

        $doc->update([
            'status' => 'rejected',
            'reject_reason' => $data['reject_reason'],
        ]);

        return back()->with('success', '已驳回「' . ($doc->type === 'license' ? '营业执照' : ($doc->type === 'id_card' ? '身份证' : '授权书')) . '」');
    }

    public function approveAllDocs($id)
    {
        $enterprise = Enterprise::findOrFail($id);

        EnterpriseDoc::where('enterprise_id', $id)->where('status', 'pending')->update(['status' => 'approved']);

        return back()->with('success', '已通过全部资质文件');
    }

    public function viewDoc($docId)
    {
        $doc = EnterpriseDoc::findOrFail($docId);
        $path = storage_path('app/' . $doc->file_path);
        if (!file_exists($path)) {
            abort(404, '文件不存在');
        }
        return response()->file($path);
    }

    public function edit($id)
    {
        $enterprise = Enterprise::with('user')->findOrFail($id);
        $colleges = College::orderBy('name')->get();
        return view('admin.enterprises.edit', compact('enterprise', 'colleges'));
    }

    public function update(Request $request, $id)
    {
        $enterprise = Enterprise::findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string|max:200',
            'contact_name' => 'required|string|max:30',
            'contact_phone' => 'required|string|max:11',
            'industry' => 'required|string|max:50',
            'scale' => 'nullable|string|max:30',
            'intro' => 'nullable|string|max:500',
            'credit_code' => 'required|string|max:50',
            'college_id' => 'nullable|exists:colleges,id',
            'status' => 'required|in:pending,approved,rejected',
        ]);

        $enterprise->fill($data)->save();

        return redirect()->route('admin.enterprises', ['status' => $request->query('status', 'all')])
            ->with('success', '已更新「' . $enterprise->name . '」');
    }

    public function destroy($id)
    {
        $enterprise = Enterprise::findOrFail($id);
        $name = $enterprise->name;

        // Delete associated docs files
        foreach ($enterprise->docs as $doc) {
            if (\Storage::exists($doc->file_path)) {
                \Storage::delete($doc->file_path);
            }
            $doc->delete();
        }

        // Delete associated user
        if ($enterprise->user) {
            $enterprise->user->delete();
        }

        $enterprise->delete();

        return back()->with('success', '已删除「' . $name . '」');
    }
}
