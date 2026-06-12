@extends('layouts.admin')

@section('title', '用户管理')

@section('content')
<div class="page-header">
    <h1>用户管理</h1>
</div>

@if (session('success'))
    <div style="background:#f0fdf4;color:#166534;padding:12px 16px;border-radius:8px;margin-bottom:16px;font-size:14px;">{{ session('success') }}</div>
@endif

<div style="display:flex;gap:0;margin-bottom:24px;border-bottom:2px solid #e2e8f0;">
    <a href="?" style="padding:10px 24px;text-decoration:none;font-size:14px;font-weight:500;color:{{ !request('role') ? '#3b82f6' : '#64748b' }};border-bottom:2px solid {{ !request('role') ? '#3b82f6' : 'transparent' }};margin-bottom:-2px;">全部</a>
    <a href="?role=school" style="padding:10px 24px;text-decoration:none;font-size:14px;font-weight:500;color:{{ request('role') === 'school' ? '#3b82f6' : '#64748b' }};border-bottom:2px solid {{ request('role') === 'school' ? '#3b82f6' : 'transparent' }};margin-bottom:-2px;">学校管理员</a>
    <a href="?role=enterprise" style="padding:10px 24px;text-decoration:none;font-size:14px;font-weight:500;color:{{ request('role') === 'enterprise' ? '#3b82f6' : '#64748b' }};border-bottom:2px solid {{ request('role') === 'enterprise' ? '#3b82f6' : 'transparent' }};margin-bottom:-2px;">企业</a>
    <a href="?role=student" style="padding:10px 24px;text-decoration:none;font-size:14px;font-weight:500;color:{{ request('role') === 'student' ? '#3b82f6' : '#64748b' }};border-bottom:2px solid {{ request('role') === 'student' ? '#3b82f6' : 'transparent' }};margin-bottom:-2px;">学生</a>
</div>

@if ($users->isEmpty())
    <div style="text-align:center;padding:60px 0;color:#94a3b8;">暂无用户</div>
@else
    <div style="background:#fff;border-radius:10px;box-shadow:0 1px 3px rgba(0,0,0,.06);padding:20px;">
        <table style="width:100%;border-collapse:collapse;">
            <thead>
                <tr style="text-align:left;border-bottom:1px solid #e2e8f0;">
                    <th style="padding:10px 12px;font-size:13px;color:#475569;">ID</th>
                    <th style="padding:10px 12px;font-size:13px;color:#475569;">用户名</th>
                    <th style="padding:10px 12px;font-size:13px;color:#475569;">姓名</th>
                    <th style="padding:10px 12px;font-size:13px;color:#475569;">角色</th>
                    <th style="padding:10px 12px;font-size:13px;color:#475569;">手机号</th>
                    <th style="padding:10px 12px;font-size:13px;color:#475569;">邮箱</th>
                    <th style="padding:10px 12px;font-size:13px;color:#475569;">关联</th>
                    <th style="padding:10px 12px;font-size:13px;color:#475569;">状态</th>
                    <th style="padding:10px 12px;font-size:13px;color:#475569;">注册时间</th>
                    <th style="padding:10px 12px;font-size:13px;color:#475569;">操作</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                <tr style="border-bottom:1px solid #f1f5f9;">
                    <td style="padding:10px 12px;font-size:14px;">{{ $user->id }}</td>
                    <td style="padding:10px 12px;font-size:14px;">{{ $user->username ?? '-' }}</td>
                    <td style="padding:10px 12px;font-size:14px;">{{ $user->name }}</td>
                    <td style="padding:10px 12px;font-size:14px;">
                        <span style="display:inline-block;padding:2px 8px;border-radius:10px;font-size:12px;
                            @if($user->role === 'school') background:#dbeafe;color:#1e40af;
                            @elseif($user->role === 'enterprise') background:#fef3c7;color:#92400e;
                            @else background:#d1fae5;color:#065f46;
                            @endif
                        ">{{ $user->role === 'school' ? '管理员' : ($user->role === 'enterprise' ? '企业' : '学生') }}</span>
                    </td>
                    <td style="padding:10px 12px;font-size:14px;">{{ $user->phone }}</td>
                    <td style="padding:10px 12px;font-size:14px;color:#64748b;">{{ $user->email ?? '-' }}</td>
                    <td style="padding:10px 12px;font-size:14px;color:#64748b;">{{ $user->enterprise->name ?? '-' }}</td>
                    <td style="padding:10px 12px;">
                        <span style="display:inline-block;padding:2px 8px;border-radius:10px;font-size:12px;
                            @if($user->status === 'active') background:#d1fae5;color:#065f46;
                            @else background:#fee2e2;color:#991b1b;
                            @endif
                        ">{{ $user->status === 'active' ? '正常' : '禁用' }}</span>
                    </td>
                    <td style="padding:10px 12px;font-size:14px;color:#64748b;">{{ $user->created_at->format('Y-m-d') }}</td>
                    <td style="padding:10px 12px;">
                        <form method="POST" action="{{ url('admin/users/' . $user->id . '/toggle') }}" style="display:inline;">
                            @csrf
                            <button type="submit" style="color:{{ $user->status === 'active' ? '#ef4444' : '#16a34a' }};border:none;background:none;cursor:pointer;font-size:13px;" onclick="return confirm('{{ $user->status === 'active' ? '确定禁用此账号？' : '确定启用此账号？' }}')">{{ $user->status === 'active' ? '禁用' : '启用' }}</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div style="margin-top:16px;">{{ $users->links() }}</div>
    </div>
@endif
@endsection
