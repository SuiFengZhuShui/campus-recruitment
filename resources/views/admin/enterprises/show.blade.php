@extends('layouts.admin')

@section('title', '企业详情')

@section('content')
<a href="{{ url('admin/enterprises?status=' . request('from', $enterprise->status)) }}" class="btn-back">&larr; 返回企业管理</a>

<div class="page-header mt-xl">
    <h1>{{ $enterprise->name }}</h1>
    <span class="badge mt-sm
        @if($enterprise->status === 'pending') badge-pending
        @elseif($enterprise->status === 'approved') badge-approved
        @else badge-rejected
        @endif
    ">
        {{ $enterprise->status === 'pending' ? '待审核' : ($enterprise->status === 'approved' ? '已通过' : '已驳回') }}
    </span>
</div>

@if (session('success'))
    <div class="flash flash-success">{{ session('success') }}</div>
@endif

<div class="detail-grid">
    <!-- 企业信息 -->
    <div class="card">
        <h3 class="section-title">企业信息</h3>
        <table class="info-table">
            <tr><td class="info-label">企业名称</td><td>{{ $enterprise->name }}</td></tr>
            <tr><td class="info-label">信用代码</td><td>{{ $enterprise->credit_code }}</td></tr>
            <tr><td class="info-label">所属行业</td><td>{{ $enterprise->industry }}</td></tr>
            <tr><td class="info-label">企业规模</td><td>{{ $enterprise->scale ?? '-' }}</td></tr>
            <tr><td class="info-label">联系人</td><td>{{ $enterprise->contact_name }}</td></tr>
            <tr><td class="info-label">联系电话</td><td>{{ $enterprise->contact_phone }}</td></tr>
            <tr><td class="info-label">邮箱</td><td>{{ $enterprise->email }}</td></tr>
            <tr><td class="info-label">登录账号</td><td>{{ $enterprise->user->username ?? '-' }}</td></tr>
            <tr><td class="info-label">手机号</td><td>{{ $enterprise->user->phone ?? '-' }}</td></tr>
            @if ($enterprise->college_id)
                <tr><td class="info-label">所属学院</td><td>{{ $enterprise->college->name ?? '-' }}</td></tr>
            @endif
            @if ($enterprise->intro)
                <tr><td class="info-label">简介</td><td>{{ $enterprise->intro }}</td></tr>
            @endif
            <tr><td class="info-label">注册时间</td><td>{{ $enterprise->created_at->format('Y-m-d H:i') }}</td></tr>
        </table>
    </div>

    <!-- 资质文件 -->
    <div class="card">
        <h3 class="section-title">资质文件</h3>
        @if ($enterprise->docs->isEmpty())
            <div class="text-light text-base">未上传资质文件</div>
        @else
            @foreach ($enterprise->docs as $doc)
                <div class="file-card">
                    <div class="file-card-header">
                        <strong class="file-card-title">
                            {{ $doc->type === 'license' ? '营业执照' : ($doc->type === 'id_card' ? '身份证' : '授权书') }}
                        </strong>
                        <span class="badge badge-sm
                            @if($doc->status === 'pending') badge-pending
                            @elseif($doc->status === 'approved') badge-approved
                            @else badge-rejected
                            @endif
                        ">{{ $doc->status === 'pending' ? '待审' : ($doc->status === 'approved' ? '已通过' : '不通过') }}</span>
                    </div>
                    <div class="text-sm text-muted">文件名：{{ $doc->file_name }}</div>
                    <div class="text-sm mt-xs">
                        <a href="{{ url('admin/docs/' . $doc->id . '/view') }}" target="_blank" class="text-primary">📄 查看文件</a>
                    </div>
                    @if ($doc->reject_reason)
                        <div class="text-sm text-danger mt-xs">不通过原因：{{ $doc->reject_reason }}</div>
                    @endif
                    @if ($enterprise->status === 'pending' && $doc->status === 'pending')
                    <div class="flex gap-sm mt-sm">
                        <form method="POST" action="{{ url('admin/enterprises/' . $enterprise->id . '/docs/' . $doc->id . '/approve') }}" class="form-inline">
                            @csrf
                            <button type="submit" class="btn btn-success btn-xs">通过</button>
                        </form>
                        <form method="POST" action="{{ url('admin/enterprises/' . $enterprise->id . '/docs/' . $doc->id . '/reject') }}" class="form-inline" style="flex:1;">
                            @csrf
                            <div class="flex gap-xs">
                                <input type="text" name="reject_reason" required placeholder="不通过原因" class="form-input" style="flex:1;padding:4px 8px;font-size:12px;border-color:#fca5a5;">
                                <button type="submit" class="btn btn-danger btn-xs" style="white-space:nowrap;">驳回</button>
                            </div>
                        </form>
                    </div>
                    @endif
                </div>
            @endforeach
            @if ($enterprise->status === 'pending' && $enterprise->docs->where('status', 'pending')->count() > 1)
            <form method="POST" action="{{ url('admin/enterprises/' . $enterprise->id . '/docs/approve-all') }}" class="mt-sm">
                @csrf
                <button type="submit" class="btn btn-success btn-sm">全部通过</button>
            </form>
            @endif
        @endif
    </div>
</div>

<!-- 审核操作 -->
@if ($enterprise->status === 'pending')
<div class="card mt-2xl">
    <h3 class="section-title">审核操作</h3>
    <div class="flex gap-xl flex-wrap" style="align-items:flex-start;">
        <!-- 通过 -->
        <form method="POST" action="{{ url('admin/enterprises/' . $enterprise->id . '/approve') }}" style="flex:1;min-width:280px;background:#f0fdf4;padding:20px;border-radius:10px;">
            @csrf
            <h4 class="text-base font-semibold mb-md" style="color:#166534;">✓ 审核通过</h4>
            <div class="form-group mb-md">
                <label class="form-label">指定学院</label>
                <select name="college_id" required class="form-input">
                    <option value="">请选择学院</option>
                    @foreach (\App\Models\College::orderBy('name')->get() as $college)
                        <option value="{{ $college->id }}">{{ $college->name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-success">确认通过</button>
        </form>

        <!-- 驳回 -->
        <form method="POST" action="{{ url('admin/enterprises/' . $enterprise->id . '/reject') }}" style="flex:1;min-width:280px;background:#fef2f2;padding:20px;border-radius:10px;">
            @csrf
            <h4 class="text-base font-semibold mb-md" style="color:#991b1b;">✗ 驳回</h4>
            <div class="form-group mb-md">
                <label class="form-label">驳回原因（必填）</label>
                <textarea name="audit_remark" required rows="3" class="form-input" placeholder="请填写驳回原因..."></textarea>
            </div>
            <button type="submit" class="btn btn-danger">确认驳回</button>
        </form>
    </div>
</div>
@endif

@if ($enterprise->audit_remark)
<div class="card mt-2xl" style="border-left:4px solid #ef4444;background:#fef2f2;">
    <strong class="text-danger-dark">驳回原因：</strong>
    <span class="text-base" style="color:#7f1d1d;">{{ $enterprise->audit_remark }}</span>
</div>
@endif
@endsection
