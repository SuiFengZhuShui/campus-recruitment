@extends('layouts.admin')

@section('title', '学院管理员')

@section('content')
<div class="page-header">
    <h1>学院管理员</h1>
    <a href="{{ url('admin/admins/create') }}" class="btn btn-primary">+ 新增管理员</a>
</div>

@if (session('success'))
    <div class="flash flash-success">{{ session('success') }}</div>
@endif

<form class="flex gap-sm mb-lg" style="max-width:400px;">
    <input type="text" name="search" value="{{ $search }}" placeholder="搜索姓名 / 用户名 / 手机 / 邮箱" class="form-input" style="flex:1;height:40px;">
    <button type="submit" class="btn btn-primary" style="height:40px;padding:0 16px;">搜索</button>
    @if ($search)
        <a href="{{ url('admin/admins') }}" class="btn" style="height:40px;line-height:40px;padding:0 12px;">清除</a>
    @endif
</form>

@if ($admins->isEmpty())
    <div class="empty-state">暂无学院管理员</div>
@else
    <div class="table-card" style="overflow:hidden;">
        <table class="table">
            <thead>
                <tr style="background:#f8fafc;">
                    <th>ID</th>
                    <th>用户名</th>
                    <th>姓名</th>
                    <th>手机号</th>
                    <th>邮箱</th>
                    <th>所属学院</th>
                    <th>状态</th>
                    <th>创建时间</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($admins as $admin)
                <tr>
                    <td>{{ $admin->id }}</td>
                    <td>{{ $admin->username }}</td>
                    <td>{{ $admin->name }}</td>
                    <td>{{ $admin->phone }}</td>
                    <td>{{ $admin->email }}</td>
                    <td>{{ $admin->college->name ?? '-' }}</td>
                    <td>
                        <span class="badge badge-sm {{ $admin->status === 'active' ? 'badge-approved' : 'badge-rejected' }}">
                            {{ $admin->status === 'active' ? '正常' : '已禁用' }}
                        </span>
                    </td>
                    <td class="text-muted">{{ $admin->created_at->format('Y-m-d') }}</td>
                    <td>
                        <div class="flex gap-xs" style="flex-shrink:0;white-space:nowrap;">
                            <a href="{{ url('admin/admins/' . $admin->id . '/edit') }}" class="btn btn-primary btn-xs">编辑</a>
                            <form method="POST" action="{{ url('admin/admins/' . $admin->id . '/toggle') }}" style="display:flex;margin:0;padding:0;">@csrf<button type="submit" class="btn btn-xs {{ $admin->status === 'active' ? 'btn-danger' : 'btn-success' }}">{{ $admin->status === 'active' ? '禁用' : '启用' }}</button></form>
                            <form method="POST" action="{{ url('admin/admins/' . $admin->id) }}" style="display:flex;margin:0;padding:0;" onsubmit="return confirm('确定删除「{{ $admin->name }}」？');">@csrf @method('DELETE')<button type="submit" class="btn btn-danger btn-xs">删除</button></form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="pagination-container">{{ $admins->links() }}</div>
@endif
@endsection
