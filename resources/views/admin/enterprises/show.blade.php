@extends('layouts.admin')

@section('title', '企业详情')

@section('content')
<a href="{{ url('admin/enterprises?status=' . $enterprise->status) }}" style="display:inline-flex;align-items:center;gap:4px;padding:8px 18px;background:linear-gradient(135deg,#c7915c,#d4a574);color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:500;text-decoration:none;">&larr; 返回企业审核</a>

<div class="page-header" style="margin-top:20px;">
    <h1>{{ $enterprise->name }}</h1>
    <span style="display:inline-block;margin-top:6px;padding:4px 12px;border-radius:20px;font-size:12px;font-weight:500;
        @if($enterprise->status === 'pending') background:#fef3c7;color:#92400e;
        @elseif($enterprise->status === 'approved') background:#d1fae5;color:#065f46;
        @else background:#fee2e2;color:#991b1b;
        @endif
    ">
        {{ $enterprise->status === 'pending' ? '待审核' : ($enterprise->status === 'approved' ? '已通过' : '已驳回') }}
    </span>
</div>

@if (session('success'))
    <div style="background:#f0fdf4;color:#166534;padding:12px 16px;border-radius:8px;margin-bottom:16px;font-size:14px;">{{ session('success') }}</div>
@endif

<div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;">
    <!-- 企业信息 -->
    <div style="background:#fff;padding:24px;border-radius:10px;box-shadow:0 1px 3px rgba(0,0,0,.06);">
        <h3 style="font-size:16px;margin-bottom:16px;color:#1e293b;">企业信息</h3>
        <table style="width:100%;font-size:14px;">
            <tr><td style="padding:6px 0;color:#64748b;width:100px;">企业名称</td><td>{{ $enterprise->name }}</td></tr>
            <tr><td style="padding:6px 0;color:#64748b;">信用代码</td><td>{{ $enterprise->credit_code }}</td></tr>
            <tr><td style="padding:6px 0;color:#64748b;">所属行业</td><td>{{ $enterprise->industry }}</td></tr>
            <tr><td style="padding:6px 0;color:#64748b;">企业规模</td><td>{{ $enterprise->scale ?? '-' }}</td></tr>
            <tr><td style="padding:6px 0;color:#64748b;">联系人</td><td>{{ $enterprise->contact_name }}</td></tr>
            <tr><td style="padding:6px 0;color:#64748b;">联系电话</td><td>{{ $enterprise->contact_phone }}</td></tr>
            <tr><td style="padding:6px 0;color:#64748b;">邮箱</td><td>{{ $enterprise->email }}</td></tr>
            <tr><td style="padding:6px 0;color:#64748b;">登录账号</td><td>{{ $enterprise->user->username ?? '-' }}</td></tr>
            <tr><td style="padding:6px 0;color:#64748b;">手机号</td><td>{{ $enterprise->user->phone ?? '-' }}</td></tr>
            @if ($enterprise->college_id)
                <tr><td style="padding:6px 0;color:#64748b;">所属学院</td><td>{{ $enterprise->college->name ?? '-' }}</td></tr>
            @endif
            @if ($enterprise->intro)
                <tr><td style="padding:6px 0;color:#64748b;">简介</td><td>{{ $enterprise->intro }}</td></tr>
            @endif
            <tr><td style="padding:6px 0;color:#64748b;">注册时间</td><td>{{ $enterprise->created_at->format('Y-m-d H:i') }}</td></tr>
        </table>
    </div>

    <!-- 资质文件 -->
    <div style="background:#fff;padding:24px;border-radius:10px;box-shadow:0 1px 3px rgba(0,0,0,.06);">
        <h3 style="font-size:16px;margin-bottom:16px;color:#1e293b;">资质文件</h3>
        @if ($enterprise->docs->isEmpty())
            <div style="color:#94a3b8;font-size:14px;">未上传资质文件</div>
        @else
            @foreach ($enterprise->docs as $doc)
                <div style="margin-bottom:16px;padding:12px;border:1px solid #e2e8f0;border-radius:8px;">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:4px;">
                        <strong style="font-size:14px;">
                            {{ $doc->type === 'license' ? '营业执照' : ($doc->type === 'id_card' ? '身份证' : '授权书') }}
                        </strong>
                        <span style="font-size:12px;padding:2px 8px;border-radius:10px;
                            @if($doc->status === 'pending') background:#fef3c7;color:#92400e;
                            @elseif($doc->status === 'approved') background:#d1fae5;color:#065f46;
                            @else background:#fee2e2;color:#991b1b;
                            @endif
                        ">{{ $doc->status === 'pending' ? '待审' : ($doc->status === 'approved' ? '已通过' : '不通过') }}</span>
                    </div>
                    <div style="font-size:13px;color:#64748b;">文件名：{{ $doc->file_name }}</div>
                    @if ($doc->reject_reason)
                        <div style="font-size:13px;color:#ef4444;margin-top:4px;">不通过原因：{{ $doc->reject_reason }}</div>
                    @endif
                    @if ($enterprise->status === 'pending' && $doc->status === 'pending')
                    <div style="display:flex;gap:8px;margin-top:8px;">
                        <form method="POST" action="{{ url('admin/enterprises/' . $enterprise->id . '/docs/' . $doc->id . '/approve') }}" style="display:inline;">
                            @csrf
                            <button type="submit" style="padding:4px 12px;background:#16a34a;color:#fff;border:none;border-radius:4px;font-size:12px;cursor:pointer;">通过</button>
                        </form>
                        <form method="POST" action="{{ url('admin/enterprises/' . $enterprise->id . '/docs/' . $doc->id . '/reject') }}" style="display:inline;flex:1;">
                            @csrf
                            <div style="display:flex;gap:4px;">
                                <input type="text" name="reject_reason" required placeholder="不通过原因" style="flex:1;padding:4px 8px;border:1px solid #fca5a5;border-radius:4px;font-size:12px;">
                                <button type="submit" style="padding:4px 12px;background:#dc2626;color:#fff;border:none;border-radius:4px;font-size:12px;cursor:pointer;white-space:nowrap;">驳回</button>
                            </div>
                        </form>
                    </div>
                    @endif
                </div>
            @endforeach
            @if ($enterprise->status === 'pending' && $enterprise->docs->where('status', 'pending')->count() > 1)
            <form method="POST" action="{{ url('admin/enterprises/' . $enterprise->id . '/docs/approve-all') }}" style="margin-top:8px;">
                @csrf
                <button type="submit" style="padding:6px 16px;background:#16a34a;color:#fff;border:none;border-radius:6px;font-size:13px;cursor:pointer;">全部通过</button>
            </form>
            @endif
        @endif
    </div>
</div>

<!-- 审核操作 -->
@if ($enterprise->status === 'pending')
<div style="background:#fff;padding:24px;border-radius:10px;box-shadow:0 1px 3px rgba(0,0,0,.06);margin-top:24px;">
    <h3 style="font-size:16px;margin-bottom:16px;color:#1e293b;">审核操作</h3>
    <div style="display:flex;gap:20px;align-items:flex-start;flex-wrap:wrap;">
        <!-- 通过 -->
        <form method="POST" action="{{ url('admin/enterprises/' . $enterprise->id . '/approve') }}" style="flex:1;min-width:280px;background:#f0fdf4;padding:20px;border-radius:10px;">
            @csrf
            <h4 style="font-size:14px;margin-bottom:12px;color:#166534;">✓ 审核通过</h4>
            <div class="form-group" style="margin-bottom:12px;">
                <label style="display:block;font-size:13px;color:#475569;margin-bottom:4px;">指定学院</label>
                <select name="college_id" required style="width:100%;padding:8px 12px;border:1px solid #cbd5e1;border-radius:6px;font-size:14px;">
                    <option value="">请选择学院</option>
                    @foreach (\App\Models\College::orderBy('name')->get() as $college)
                        <option value="{{ $college->id }}">{{ $college->name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" style="padding:10px 24px;background:#16a34a;color:#fff;border:none;border-radius:6px;font-size:14px;cursor:pointer;">确认通过</button>
        </form>

        <!-- 驳回 -->
        <form method="POST" action="{{ url('admin/enterprises/' . $enterprise->id . '/reject') }}" style="flex:1;min-width:280px;background:#fef2f2;padding:20px;border-radius:10px;">
            @csrf
            <h4 style="font-size:14px;margin-bottom:12px;color:#991b1b;">✗ 驳回</h4>
            <div class="form-group" style="margin-bottom:12px;">
                <label style="display:block;font-size:13px;color:#475569;margin-bottom:4px;">驳回原因（必填）</label>
                <textarea name="audit_remark" required rows="3" style="width:100%;padding:8px 12px;border:1px solid #cbd5e1;border-radius:6px;font-size:14px;resize:vertical;" placeholder="请填写驳回原因..."></textarea>
            </div>
            <button type="submit" style="padding:10px 24px;background:#dc2626;color:#fff;border:none;border-radius:6px;font-size:14px;cursor:pointer;">确认驳回</button>
        </form>
    </div>
</div>
@endif

@if ($enterprise->audit_remark)
<div style="background:#fef2f2;padding:20px;border-radius:10px;margin-top:24px;border-left:4px solid #ef4444;">
    <strong style="color:#991b1b;">驳回原因：</strong>
    <span style="font-size:14px;color:#7f1d1d;">{{ $enterprise->audit_remark }}</span>
</div>
@endif
@endsection
