<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $role = $request->query('role');
        $search = $request->query('search');

        $query = User::with('enterprise:id,user_id,name')->orderBy('id', 'asc');

        if ($role === 'school') {
            $query->whereIn('role', ['school', 'college']);
        } elseif ($role && in_array($role, ['college', 'enterprise', 'student'])) {
            $query->where('role', $role);
        }

        if ($search) {
            $escapedSearch = str_replace(['%', '_'], ['\%', '\_'], $search);
            $query->where(function ($q) use ($escapedSearch) {
                $q->where('name', 'like', "%{$escapedSearch}%")
                  ->orWhere('username', 'like', "%{$escapedSearch}%")
                  ->orWhere('phone', 'like', "%{$escapedSearch}%")
                  ->orWhere('email', 'like', "%{$escapedSearch}%");
            });
        }

        $users = $query->paginate(15);

        if ($role) {
            $users->appends(['role' => $role]);
        }
        if ($search) {
            $users->appends(['search' => $search]);
        }

        return view('admin.users.index', compact('users', 'role', 'search'));
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string|max:50',
            'username' => 'required|string|min:3|max:30|unique:users,username,' . $id,
            'phone' => 'required|string|min:11|max:11|regex:/^1[3-9]\d{9}$/|unique:users,phone,' . $id,
            'email' => 'required|email|max:100|unique:users,email,' . $id,
            'status' => 'required|in:active,disabled',
        ]);

        $user->fill($data)->save();

        return redirect()->route('admin.users')->with('success', '「' . $user->name . '」已更新');
    }

    public function toggle($id)
    {
        $user = User::findOrFail($id);

        $newStatus = $user->status === 'active' ? 'disabled' : 'active';
        $user->update(['status' => $newStatus]);

        return back()->with('success', '「' . $user->name . '」已' . ($newStatus === 'active' ? '启用' : '禁用'));
    }
}
