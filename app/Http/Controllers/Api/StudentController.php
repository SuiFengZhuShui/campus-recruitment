<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function profile(Request $request)
    {
        $user = auth()->user();
        if (!$user || !$user->isStudent()) {
            return response()->json(['code' => 401, 'message' => '请使用学生账号登录', 'data' => null], 401);
        }

        $student = Student::with('college', 'user:id,name,phone,email')
            ->where('user_id', $user->id)
            ->first();

        if (!$student) {
            return response()->json(['code' => 404, 'message' => '学生档案不存在', 'data' => null], 404);
        }

        return response()->json(['code' => 200, 'message' => 'success', 'data' => $student]);
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        if (!$user || !$user->isStudent()) {
            return response()->json(['code' => 401, 'message' => '请使用学生账号登录', 'data' => null], 401);
        }

        $data = $request->validate([
            'name' => 'string|max:50',
            'email' => 'email|max:100|unique:users,email,' . $user->id,
            'class_name' => 'string|max:100',
        ]);

        if (isset($data['name'])) {
            $user->update(['name' => $data['name']]);
        }
        if (isset($data['email'])) {
            $user->update(['email' => $data['email']]);
        }

        $student = Student::where('user_id', $user->id)->first();
        if ($student && isset($data['class_name'])) {
            $student->update(['class_name' => $data['class_name']]);
        }

        $student = Student::with('college')->where('user_id', $user->id)->first();

        return response()->json(['code' => 200, 'message' => '更新成功', 'data' => $student]);
    }

    public function uploadResume(Request $request)
    {
        $user = auth()->user();
        if (!$user || !$user->isStudent()) {
            return response()->json(['code' => 401, 'message' => '请使用学生账号登录', 'data' => null], 401);
        }

        $request->validate([
            'resume' => 'required|file|mimes:pdf,doc,docx|max:10240',
        ]);

        $student = Student::where('user_id', $user->id)->firstOrFail();

        // Delete old resume
        if ($student->resume_path && \Storage::exists($student->resume_path)) {
            \Storage::delete($student->resume_path);
        }

        $file = $request->file('resume');
        $filePath = $file->storeAs(
            'resumes/' . $user->id,
            uniqid() . '_' . preg_replace('/[^\w\.]/u', '_', $file->getClientOriginalName())
        );

        $student->update(['resume_path' => $filePath]);

        return response()->json([
            'code' => 200,
            'message' => '简历上传成功',
            'data' => ['resume_path' => $filePath, 'file_name' => $file->getClientOriginalName()],
        ]);
    }

    public function downloadResume($studentId = null)
    {
        $user = auth()->user();

        // Enterprise downloading a specific student's resume
        if ($studentId && $user->isEnterprise()) {
            $enterprise = \App\Models\Enterprise::where('user_id', $user->id)->firstOrFail();
            // Verify the student applied to one of this enterprise's jobs
            $hasApplied = \App\Models\Application::whereHas('job', function ($q) use ($enterprise) {
                $q->where('enterprise_id', $enterprise->id);
            })->where('student_id', $studentId)->exists();

            if (!$hasApplied) {
                return response()->json(['code' => 403, 'message' => '无权下载此简历', 'data' => null], 403);
            }

            $student = Student::findOrFail($studentId);
        } elseif (!$studentId && $user->isStudent()) {
            $student = Student::where('user_id', $user->id)->firstOrFail();
        } else {
            return response()->json(['code' => 401, 'message' => '无权访问', 'data' => null], 401);
        }

        if (!$student->resume_path || !\Storage::exists($student->resume_path)) {
            return response()->json(['code' => 404, 'message' => '未上传简历', 'data' => null], 404);
        }

        return \Storage::download($student->resume_path);
    }
}
