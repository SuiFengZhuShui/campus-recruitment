<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\College;
use App\Models\Enterprise;
use App\Models\EnterpriseDoc;
use Illuminate\Http\Request;

class EnterpriseController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'pending');

        $enterprises = Enterprise::with('user', 'docs', 'college')
            ->where('status', $status)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.enterprises.index', compact('enterprises', 'status'));
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
}
