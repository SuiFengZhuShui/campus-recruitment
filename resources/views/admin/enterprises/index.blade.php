@extends('layouts.admin')

@section('title', '企业管理')

@section('content')
<div class="page-header">
    <h1>企业管理</h1>
</div>

<div class="tabs">
    <a href="?status=all{{ $search ? '&search=' . $search : '' }}" class="tab {{ $status === 'all' ? 'active' : '' }}">全部</a>
    <a href="?status=pending{{ $search ? '&search=' . $search : '' }}" class="tab {{ $status === 'pending' ? 'active' : '' }}">待审核</a>
    <a href="?status=approved{{ $search ? '&search=' . $search : '' }}" class="tab {{ $status === 'approved' ? 'active' : '' }}">已通过</a>
    <a href="?status=rejected{{ $search ? '&search=' . $search : '' }}" class="tab {{ $status === 'rejected' ? 'active' : '' }}">已驳回</a>
</div>

@if (session('success'))
    <div class="flash flash-success">{{ session('success') }}</div>
@endif
@if (session('error'))
    <div class="flash flash-error">{{ session('error') }}</div>
@endif

<form class="flex gap-sm mb-lg" style="max-width:400px;">
    <input type="hidden" name="status" value="{{ $status }}">
    <input type="text" name="search" value="{{ $search }}" placeholder="搜索企业名称 / 联系人 / 电话 / 行业" class="form-input" style="flex:1;height:40px;">
    <button type="submit" class="btn btn-primary" style="height:40px;padding:0 16px;">搜索</button>
    @if ($search)
        <a href="?status={{ $status }}" class="btn" style="height:40px;line-height:40px;padding:0 12px;">清除</a>
    @endif
</form>

@if ($enterprises->isEmpty())
    <div class="empty-state">暂无{{ $status === 'all' ? '' : ($status === 'pending' ? '待审' : ($status === 'approved' ? '已通过' : '已驳回')) }}企业</div>
@else
    <div class="table-card" style="overflow:hidden;">
        <table class="table">
            <thead>
                <tr style="background:#f8fafc;">
                    <th>企业名称</th>
                    <th>联系人</th>
                    <th>电话</th>
                    <th>行业</th>
                    @if ($status === 'all')
                        <th>状态</th>
                    @endif
                    <th>注册时间</th>
                    @if ($status === 'rejected')
                        <th>驳回原因</th>
                    @endif
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($enterprises as $enterprise)
                <tr>
                    <td>{{ $enterprise->name }}</td>
                    <td>{{ $enterprise->contact_name }}</td>
                    <td>{{ $enterprise->contact_phone }}</td>
                    <td>{{ $enterprise->industry }}</td>
                    @if ($status === 'all')
                        <td>
                            <span class="badge badge-sm
                                @if($enterprise->status === 'pending') badge-pending
                                @elseif($enterprise->status === 'approved') badge-approved
                                @else badge-rejected
                                @endif
                            ">
                                {{ $enterprise->status === 'pending' ? '待审核' : ($enterprise->status === 'approved' ? '已通过' : '已驳回') }}
                            </span>
                        </td>
                    @endif
                    <td class="text-muted">{{ $enterprise->created_at->format('Y-m-d H:i') }}</td>
                    @if ($status === 'rejected')
                        <td class="text-danger">{{ $enterprise->audit_remark }}</td>
                    @endif
                    <td>
                        <div class="flex gap-xs" style="flex-shrink:0;white-space:nowrap;">
                            <a href="{{ url('admin/enterprises/' . $enterprise->id . '?from=' . $status) }}" class="btn btn-xs">详情</a>
                            <a href="{{ url('admin/enterprises/' . $enterprise->id . '/edit?status=' . $status) }}" class="btn btn-primary btn-xs">编辑</a>
                            <form method="POST" action="{{ url('admin/enterprises/' . $enterprise->id) }}" style="display:flex;margin:0;padding:0;" onsubmit="return confirm('确定删除「{{ $enterprise->name }}」？将同时删除关联用户和资质文件。');">@csrf @method('DELETE')<button type="submit" class="btn btn-danger btn-xs">删除</button></form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="pagination-container">{{ $enterprises->links() }}</div>
@endif
@endsection
