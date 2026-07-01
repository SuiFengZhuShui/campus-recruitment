@extends('layouts.admin')

@section('title', '岗位管理')

@section('content')
<div class="page-header">
    <h1>岗位管理</h1>
</div>

<div class="tabs">
    <a href="?status=all{{ $search ? '&search=' . $search : '' }}" class="tab {{ $status === 'all' ? 'active' : '' }}">全部</a>
    <a href="?status=active{{ $search ? '&search=' . $search : '' }}" class="tab {{ $status === 'active' ? 'active' : '' }}">上架中</a>
    <a href="?status=closed{{ $search ? '&search=' . $search : '' }}" class="tab {{ $status === 'closed' ? 'active' : '' }}">已下架</a>
</div>

@if (session('success'))
    <div class="flash flash-success">{{ session('success') }}</div>
@endif
@if (session('error'))
    <div class="flash flash-error">{{ session('error') }}</div>
@endif

<form class="flex gap-sm mb-lg" style="max-width:400px;">
    <input type="hidden" name="status" value="{{ $status }}">
    <input type="text" name="search" value="{{ $search }}" placeholder="搜索岗位 / 城市 / 专业 / 企业" class="form-input" style="flex:1;height:40px;">
    <button type="submit" class="btn btn-primary" style="height:40px;padding:0 16px;">搜索</button>
    @if ($search)
        <a href="?status={{ $status }}" class="btn" style="height:40px;line-height:40px;padding:0 12px;">清除</a>
    @endif
</form>

@if ($jobs->isEmpty())
    <div class="empty-state">暂无岗位</div>
@else
    <div class="table-card" style="overflow:hidden;">
        <table class="table">
            <thead>
                <tr style="background:#f8fafc;">
                    <th>岗位名称</th>
                    <th>企业</th>
                    <th>城市</th>
                    <th>学历</th>
                    <th>薪资</th>
                    @if ($status === 'all')
                        <th>状态</th>
                    @endif
                    <th>发布时间</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($jobs as $job)
                <tr>
                    <td>{{ $job->title }}</td>
                    <td>{{ $job->enterprise->name ?? '-' }}</td>
                    <td>{{ $job->city }}</td>
                    <td>{{ $job->education }}</td>
                    <td>{{ $job->salary_min }}-{{ $job->salary_max }}K</td>
                    @if ($status === 'all')
                        <td>
                            @if ($job->status === 'active')
                                <span class="badge badge-green">上架</span>
                            @else
                                <span class="badge badge-gray">下架</span>
                            @endif
                        </td>
                    @endif
                    <td>{{ $job->created_at->format('Y-m-d') }}</td>
                    <td>
                        <form action="{{ url('admin/jobs/' . $job->id . '/toggle') }}" method="POST" style="display:inline;" onsubmit="return confirm('确认{{ $job->status === 'active' ? '下架' : '上架' }}该岗位？')">
                            @csrf
                            <button type="submit" class="btn btn-sm {{ $job->status === 'active' ? 'btn-warning' : 'btn-primary' }}">
                                {{ $job->status === 'active' ? '下架' : '上架' }}
                            </button>
                        </form>
                        <form action="{{ url('admin/jobs/' . $job->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('确认删除「{{ $job->title }}」？此操作不可恢复。')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">删除</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="pagination">
        {{ $jobs->links() }}
    </div>
@endif
@endsection
