<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\College;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CollegeAdminController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');

        $query = User::with('college')
            ->where('role', 'college')
            ->orderBy('id', 'asc');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $admins = $query->paginate(15);

        if ($search) {
            $admins->appends(['search' => $search]);
        }

        return view('admin.admins.index', compact('admins', 'search'));
    }

    public function create()
    {
        $colleges = College::orderBy('name')->get();
        return view('admin.admins.create', compact('colleges'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'username' => 'required|string|min:3|max:30|unique:users,username',
            'name' => 'required|string|max:50',
            'phone' => 'required|string|min:11|max:11|regex:/^1[3-9]\d{9}$/|unique:users,phone',
            'email' => 'required|email|max:100|unique:users,email',
            'college_id' => 'required|exists:colleges,id',
            'password' => 'required|string|min:6|max:50',
        ]);

        User::create([
            'role' => 'college',
            'username' => $data['username'],
            'name' => $data['name'],
            'phone' => $data['phone'],
            'email' => $data['email'],
            'college_id' => $data['college_id'],
            'password' => Hash::make($data['password']),
            'status' => 'active',
        ]);

        return redirect()->route('admin.admins')->with('success', '已创建学院管理员「' . $data['name'] . '」');
    }

    public function edit($id)
    {
        $user = User::where('role', 'college')->findOrFail($id);
        $colleges = College::orderBy('name')->get();
        return view('admin.admins.edit', compact('user', 'colleges'));
    }

    public function update(Request $request, $id)
    {
        $user = User::where('role', 'college')->findOrFail($id);

        $data = $request->validate([
            'username' => 'required|string|min:3|max:30|unique:users,username,' . $id,
            'name' => 'required|string|max:50',
            'phone' => 'required|string|min:11|max:11|regex:/^1[3-9]\d{9}$/|unique:users,phone,' . $id,
            'email' => 'required|email|max:100|unique:users,email,' . $id,
            'college_id' => 'required|exists:colleges,id',
            'password' => 'nullable|string|min:6|max:50',
        ]);

        $updateData = [
            'username' => $data['username'],
            'name' => $data['name'],
            'phone' => $data['phone'],
            'email' => $data['email'],
            'college_id' => $data['college_id'],
        ];

        if (!empty($data['password'])) {
            $updateData['password'] = Hash::make($data['password']);
        }

        $user->fill($updateData)->save();

        return redirect()->route('admin.admins')->with('success', '已更新「' . $user->name . '」');
    }

    public function destroy($id)
    {
        $user = User::where('role', 'college')->findOrFail($id);
        $name = $user->name;
        $user->delete();

        return back()->with('success', '已删除「' . $name . '」');
    }

    public function toggle($id)
    {
        $user = User::where('role', 'college')->findOrFail($id);
        $newStatus = $user->status === 'active' ? 'disabled' : 'active';
        $user->update(['status' => $newStatus]);

        return back()->with('success', '「' . $user->name . '」已' . ($newStatus === 'active' ? '启用' : '禁用'));
    }
}
