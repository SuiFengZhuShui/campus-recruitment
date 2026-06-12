<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Enterprise;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function registerStudent(Request $request)
    {
        $this->checkCaptcha($request);

        $data = $request->validate([
            'username' => 'required|string|min:3|max:30|unique:users,username',
            'email' => 'required|email|max:100|unique:users,email',
            'student_no' => 'required|string|max:30|unique:students,student_no',
            'class_name' => 'required|string|max:100',
            'name' => 'required|string|max:50',
            'phone' => 'required|string|max:20|unique:users,phone',
            'password' => 'required|string|min:6|max:50',
        ]);

        $user = User::create([
            'role' => 'student',
            'username' => $data['username'],
            'email' => $data['email'],
            'name' => $data['name'],
            'phone' => $data['phone'],
            'password' => Hash::make($data['password']),
            'status' => 'active',
        ]);

        // Auto-match college by student_no prefix
        $collegeId = $this->matchCollegeByStudentNo($data['student_no']);

        Student::create([
            'user_id' => $user->id,
            'student_no' => $data['student_no'],
            'class_name' => $data['class_name'],
            'grade' => substr($data['student_no'], 0, 4),
            'college_id' => $collegeId,
        ]);

        // Update user's college_id
        if ($collegeId) {
            $user->update(['college_id' => $collegeId]);
        }

        auth()->login($user);

        return $this->respond($user);
    }

    public function registerEnterprise(Request $request)
    {
        $this->checkCaptcha($request);

        $data = $request->validate([
            'username' => 'required|string|min:3|max:30|unique:users,username',
            'name' => 'required|string|max:200',
            'credit_code' => 'required|string|max:50|unique:enterprises,credit_code',
            'industry' => 'required|string|max:50',
            'scale' => 'nullable|string|max:30',
            'intro' => 'nullable|string',
            'contact_name' => 'required|string|max:30',
            'contact_phone' => 'required|string|max:20',
            'email' => 'required|email|max:100|unique:users,email',
            'phone' => 'required|string|max:20|unique:users,phone',
            'password' => 'required|string|min:6|max:50',
            'doc_license' => 'required|file|mimes:png,jpg,jpeg,pdf|max:5120',
            'doc_id_card' => 'required|file|mimes:png,jpg,jpeg,pdf|max:5120',
            'doc_authorization' => 'required|file|mimes:png,jpg,jpeg,pdf|max:5120',
        ]);

        $user = User::create([
            'role' => 'enterprise',
            'username' => $data['username'],
            'email' => $data['email'],
            'name' => $data['contact_name'],
            'phone' => $data['phone'],
            'password' => Hash::make($data['password']),
            'status' => 'active',
        ]);

        $enterprise = Enterprise::create([
            'user_id' => $user->id,
            'name' => $data['name'],
            'credit_code' => $data['credit_code'],
            'industry' => $data['industry'],
            'scale' => $data['scale'] ?? null,
            'intro' => $data['intro'] ?? null,
            'contact_name' => $data['contact_name'],
            'contact_phone' => $data['contact_phone'],
            'email' => $data['email'],
            'status' => 'pending',
        ]);

        // Save 3 qualification docs
        $docTypes = [
            'doc_license' => 'license',
            'doc_id_card' => 'id_card',
            'doc_authorization' => 'authorization',
        ];
        foreach ($docTypes as $field => $type) {
            $file = $request->file($field);
            $fileName = $file->getClientOriginalName();
            $filePath = $file->storeAs(
                'enterprise_docs/' . $enterprise->id,
                uniqid() . '_' . preg_replace('/[^\w\.]/u', '_', $fileName)
            );

            \App\Models\EnterpriseDoc::create([
                'enterprise_id' => $enterprise->id,
                'type' => $type,
                'file_path' => $filePath,
                'file_name' => $fileName,
                'status' => 'pending',
            ]);
        }

        auth()->login($user);

        return $this->respond($user, '注册成功，请等待管理员审核');
    }

    public function login(Request $request)
    {
        $this->checkCaptcha($request);

        $data = $request->validate([
            'account' => 'required|string',
            'password' => 'required|string',
        ]);

        $account = $data['account'];
        $user = User::where('phone', $account)
            ->orWhere('username', $account)
            ->orWhere('email', $account)
            ->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'account' => ['账号或密码错误'],
            ]);
        }

        if ($user->status === 'disabled') {
            throw ValidationException::withMessages([
                'account' => ['账号已被禁用'],
            ]);
        }

        auth()->login($user);

        return $this->respond($user);
    }

    public function logout(Request $request)
    {
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['code' => 200, 'message' => '已退出登录', 'data' => null]);
    }

    public function me(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['code' => 401, 'message' => '未登录', 'data' => null], 401);
        }

        $data = [
            'id' => $user->id,
            'role' => $user->role,
            'username' => $user->username,
            'name' => $user->name,
            'phone' => $user->phone,
            'email' => $user->email,
            'status' => $user->status,
        ];

        // Load role-specific profile
        if ($user->isEnterprise()) {
            $enterprise = Enterprise::where('user_id', $user->id)->first();
            $data['enterprise'] = $enterprise;
        } elseif ($user->isStudent()) {
            $student = Student::where('user_id', $user->id)->first();
            $data['student'] = $student;
        }

        return response()->json(['code' => 200, 'message' => 'success', 'data' => $data]);
    }

    private function checkCaptcha(Request $request): void
    {
        $request->validate(['captcha' => 'required|string|size:4']);

        $stored = session('captcha_code');
        if (!$stored || strtolower($request->captcha) !== strtolower($stored)) {
            throw ValidationException::withMessages([
                'captcha' => ['验证码不正确'],
            ]);
        }

        // Invalidate captcha after use
        session()->forget('captcha_code');
    }

    private function matchCollegeByStudentNo(string $studentNo): ?int
    {
        $rules = \App\Models\StudentIdRule::orderBy('prefix')->get();
        foreach ($rules as $rule) {
            if (strpos($studentNo, $rule->prefix) === 0) {
                return $rule->college_id;
            }
        }
        return null;
    }

    private function respond(User $user, string $message = 'success'): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'code' => 200,
            'message' => $message,
            'data' => [
                'id' => $user->id,
                'role' => $user->role,
                'username' => $user->username,
                'name' => $user->name,
                'phone' => $user->phone,
                'email' => $user->email,
            ],
        ]);
    }
}
