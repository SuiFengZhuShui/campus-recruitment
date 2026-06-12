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

        $query = User::with('enterprise:id,user_id,name')->orderBy('created_at', 'desc');

        if ($role && in_array($role, ['school', 'enterprise', 'student'])) {
            $query->where('role', $role);
        }

        $users = $query->paginate(15);

        return view('admin.users.index', compact('users', 'role'));
    }

    public function toggle($id)
    {
        $user = User::findOrFail($id);

        $newStatus = $user->status === 'active' ? 'disabled' : 'active';
        $user->update(['status' => $newStatus]);

        return back()->with('success', '「' . $user->name . '」已' . ($newStatus === 'active' ? '启用' : '禁用'));
    }
}
