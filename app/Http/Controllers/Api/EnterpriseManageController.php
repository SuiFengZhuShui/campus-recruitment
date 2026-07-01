<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Enterprise;
use App\Models\EnterpriseDoc;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class EnterpriseManageController extends Controller
{
    // === Profile ===

    public function profile()
    {
        $user = auth()->user();
        $enterprise = Enterprise::with('docs', 'college:id,name')->where('user_id', $user->id)->firstOrFail();

        return response()->json([
            'code' => 200,
            'message' => 'success',
            'data' => $enterprise,
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        $enterprise = Enterprise::where('user_id', $user->id)->firstOrFail();

        $data = $request->validate([
            'name' => 'string|max:200',
            'industry' => 'string|max:50',
            'scale' => 'nullable|string|max:30',
            'intro' => 'nullable|string|max:500',
            'contact_name' => 'string|max:30',
            'contact_phone' => 'string|max:11',
            'email' => 'email|max:100|unique:users,email,' . $user->id,
        ]);

        if (isset($data['email'])) {
            $user->update(['email' => $data['email']]);
        }
        if (isset($data['name'])) {
            $user->update(['name' => $data['name']]);
        }

        $enterpriseData = array_intersect_key($data, array_flip(['name', 'industry', 'scale', 'intro', 'contact_name', 'contact_phone']));
        if ($enterpriseData) {
            $enterprise->fill($enterpriseData)->save();
        }

        return response()->json(['code' => 200, 'message' => '更新成功', 'data' => $enterprise->fresh()]);
    }

    public function updatePassword(Request $request)
    {
        $user = auth()->user();

        $data = $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:6|max:50|confirmed',
        ]);

        if (!Hash::check($data['current_password'], $user->password)) {
            return response()->json(['code' => 422, 'message' => '当前密码不正确', 'errors' => ['current_password' => ['当前密码不正确']]], 422);
        }

        $user->update(['password' => Hash::make($data['password'])]);

        return response()->json(['code' => 200, 'message' => '密码已修改', 'data' => null]);
    }

    // === Documents ===

    public function uploadDoc(Request $request)
    {
        $user = auth()->user();
        $enterprise = Enterprise::where('user_id', $user->id)->firstOrFail();

        $request->validate([
            'type' => ['required', Rule::in(['license', 'id_card', 'authorization'])],
            'file' => 'required|file|mimes:jpg,jpeg,png,pdf|max:10240',
        ]);

        // Delete old doc of same type
        $oldDoc = EnterpriseDoc::where('enterprise_id', $enterprise->id)
            ->where('type', $request->type)
            ->first();
        if ($oldDoc && \Storage::exists($oldDoc->file_path)) {
            \Storage::delete($oldDoc->file_path);
            $oldDoc->delete();
        }

        $file = $request->file('file');
        $filePath = $file->storeAs(
            'enterprises/' . $enterprise->id . '/docs',
            $request->type . '_' . time() . '.' . $file->getClientOriginalExtension()
        );

        EnterpriseDoc::create([
            'enterprise_id' => $enterprise->id,
            'type' => $request->type,
            'file_path' => $filePath,
            'file_name' => $file->getClientOriginalName(),
            'status' => 'pending',
        ]);

        return response()->json(['code' => 200, 'message' => '上传成功', 'data' => null]);
    }

    public function deleteDoc($id)
    {
        $user = auth()->user();
        $enterprise = Enterprise::where('user_id', $user->id)->firstOrFail();

        $doc = EnterpriseDoc::where('id', $id)->where('enterprise_id', $enterprise->id)->firstOrFail();
        if (\Storage::exists($doc->file_path)) {
            \Storage::delete($doc->file_path);
        }
        $doc->delete();

        return response()->json(['code' => 200, 'message' => '已删除', 'data' => null]);
    }

    // === Resumes ===

    public function resumes(Request $request)
    {
        $user = auth()->user();
        $enterprise = Enterprise::where('user_id', $user->id)->firstOrFail();

        if ($enterprise->status !== 'approved') {
            return response()->json(['code' => 403, 'message' => '企业未通过审核', 'data' => null], 403);
        }

        $search = $request->query('search');
        $collegeId = $request->query('college_id');

        $query = Student::with('user:id,name,phone,email', 'college:id,name')
            ->whereHas('user', function ($q) {
                $q->where('status', 'active');
            });

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                })->orWhere('student_no', 'like', "%{$search}%")
                  ->orWhere('class_name', 'like', "%{$search}%");
            });
        }
        if ($collegeId) {
            $query->where('college_id', $collegeId);
        }

        $students = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json([
            'code' => 200,
            'message' => 'success',
            'data' => [
                'list' => $students->items(),
                'meta' => [
                    'current_page' => $students->currentPage(),
                    'per_page' => $students->perPage(),
                    'total' => $students->total(),
                    'last_page' => $students->lastPage(),
                ],
            ],
        ]);
    }
}
