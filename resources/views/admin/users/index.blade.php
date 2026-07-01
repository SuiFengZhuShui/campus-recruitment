@extends('layouts.admin')

@section('title', '用户管理')

@section('content')
<div class="page-header">
    <h1>用户管理</h1>
</div>

@if (session('success'))
    <div class="flash flash-success">{{ session('success') }}</div>
@endif

<form style="display:flex;gap:var(--space-md);margin-bottom:var(--space-lg);align-items:center;">
    @if(request('role'))
        <input type="hidden" name="role" value="{{ request('role') }}">
    @endif
    <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="搜索姓名 / 用户名 / 手机号 / 邮箱" class="input" style="flex:1;max-width:400px;height:40px;padding:0 16px;font-size:15px;">
    <button type="submit" class="btn btn-primary" style="height:40px;padding:0 24px;font-size:15px;">搜索</button>
    @if(!empty($search))
        <a href="?{{ request('role') ? 'role='.request('role') : '' }}" style="height:40px;line-height:40px;padding:0 16px;border-radius:var(--radius-md);border:1px solid var(--color-border-light);color:var(--color-text);text-decoration:none;font-size:14px;white-space:nowrap;">✕ 清除</a>
    @endif
</form>

<div class="tabs">
    <a href="?" class="tab {{ !request('role') ? 'active' : '' }}">全部</a>
    <a href="?role=school" class="tab {{ request('role') === 'school' ? 'active' : '' }}">管理员</a>
    <a href="?role=enterprise" class="tab {{ request('role') === 'enterprise' ? 'active' : '' }}">企业</a>
    <a href="?role=student" class="tab {{ request('role') === 'student' ? 'active' : '' }}">学生</a>
</div>

@if ($users->isEmpty())
    <div class="empty-state">暂无用户</div>
@else
    <div class="table-card">
        <table class="table">
            <thead>
                <tr>
                    <th style="width:50px">ID</th>
                    <th style="width:100px">用户名</th>
                    <th style="width:80px">姓名</th>
                    <th style="width:100px">角色</th>
                    <th style="width:120px">手机号</th>
                    <th style="width:140px">邮箱</th>
                    <th style="width:90px">关联</th>
                    <th style="width:70px">状态</th>
                    <th style="width:100px">注册时间</th>
                    <th style="width:140px">操作</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->username ?? '-' }}</td>
                    <td>{{ $user->name }}</td>
                    <td>
                        <span class="badge badge-sm
                            @if($user->role === 'school') tag-blue
                            @elseif($user->role === 'college') tag-purple
                            @elseif($user->role === 'enterprise') tag-yellow
                            @else tag-green
                            @endif
                        ">{{ $user->role === 'school' ? '学校管理员' : ($user->role === 'college' ? '学院管理员' : ($user->role === 'enterprise' ? '企业' : '学生')) }}</span>
                    </td>
                    <td>{{ $user->phone }}</td>
                    <td class="text-muted">{{ $user->email ?? '-' }}</td>
                    <td class="text-muted">{{ $user->enterprise->name ?? '-' }}</td>
                    <td>
                        <span class="badge badge-sm
                            @if($user->status === 'active') badge-active
                            @else badge-disabled
                            @endif
                        ">{{ $user->status === 'active' ? '正常' : '禁用' }}</span>
                    </td>
                    <td class="text-muted">{{ $user->created_at->format('Y-m-d') }}</td>
                    <td>
                        <div class="flex gap-xs" style="flex-shrink:0;white-space:nowrap;">
                            <a href="{{ url('admin/users/' . $user->id . '/edit') }}" class="btn btn-primary btn-xs">编辑</a>
                            <form method="POST" action="{{ url('admin/users/' . $user->id . '/toggle') }}" style="display:flex;margin:0;padding:0;" onsubmit="return confirm('{{ $user->status === 'active' ? '确定禁用此账号？' : '确定启用此账号？' }}')">@csrf<button type="submit" class="btn btn-xs {{ $user->status === 'active' ? 'btn-danger' : 'btn-success' }}">{{ $user->status === 'active' ? '禁用' : '启用' }}</button></form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="pagination-container">{{ $users->links() }}</div>
    </div>
@endif
@endsection
