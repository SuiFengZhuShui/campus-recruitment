@extends('layouts.admin')

@section('title', '企业审核')

@section('content')
<div class="page-header">
    <h1>企业审核</h1>
</div>

<div style="display:flex;gap:0;margin-bottom:24px;border-bottom:2px solid #e2e8f0;">
    <a href="?status=pending" style="padding:10px 24px;text-decoration:none;font-size:14px;font-weight:500;color:{{ $status === 'pending' ? '#3b82f6' : '#64748b' }};border-bottom:2px solid {{ $status === 'pending' ? '#3b82f6' : 'transparent' }};margin-bottom:-2px;">待审核</a>
    <a href="?status=approved" style="padding:10px 24px;text-decoration:none;font-size:14px;font-weight:500;color:{{ $status === 'approved' ? '#3b82f6' : '#64748b' }};border-bottom:2px solid {{ $status === 'approved' ? '#3b82f6' : 'transparent' }};margin-bottom:-2px;">已通过</a>
    <a href="?status=rejected" style="padding:10px 24px;text-decoration:none;font-size:14px;font-weight:500;color:{{ $status === 'rejected' ? '#3b82f6' : '#64748b' }};border-bottom:2px solid {{ $status === 'rejected' ? '#3b82f6' : 'transparent' }};margin-bottom:-2px;">已驳回</a>
</div>

@if (session('success'))
    <div style="background:#f0fdf4;color:#166534;padding:12px 16px;border-radius:8px;margin-bottom:16px;font-size:14px;">{{ session('success') }}</div>
@endif
@if (session('error'))
    <div style="background:#fef2f2;color:#991b1b;padding:12px 16px;border-radius:8px;margin-bottom:16px;font-size:14px;">{{ session('error') }}</div>
@endif

@if ($enterprises->isEmpty())
    <div style="text-align:center;padding:60px 0;color:#94a3b8;">暂无{{ $status === 'pending' ? '待审' : ($status === 'approved' ? '已通过' : '已驳回') }}企业</div>
@else
    <table style="width:100%;border-collapse:collapse;background:#fff;border-radius:10px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,.06);">
        <thead>
            <tr style="background:#f8fafc;text-align:left;">
                <th style="padding:14px 18px;font-size:13px;font-weight:600;color:#475569;">企业名称</th>
                <th style="padding:14px 18px;font-size:13px;font-weight:600;color:#475569;">联系人</th>
                <th style="padding:14px 18px;font-size:13px;font-weight:600;color:#475569;">电话</th>
                <th style="padding:14px 18px;font-size:13px;font-weight:600;color:#475569;">行业</th>
                <th style="padding:14px 18px;font-size:13px;font-weight:600;color:#475569;">注册时间</th>
                @if ($status === 'rejected')
                    <th style="padding:14px 18px;font-size:13px;font-weight:600;color:#475569;">驳回原因</th>
                @endif
                <th style="padding:14px 18px;font-size:13px;font-weight:600;color:#475569;">操作</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($enterprises as $enterprise)
            <tr style="border-top:1px solid #e2e8f0;">
                <td style="padding:14px 18px;font-size:14px;">{{ $enterprise->name }}</td>
                <td style="padding:14px 18px;font-size:14px;">{{ $enterprise->contact_name }}</td>
                <td style="padding:14px 18px;font-size:14px;">{{ $enterprise->contact_phone }}</td>
                <td style="padding:14px 18px;font-size:14px;">{{ $enterprise->industry }}</td>
                <td style="padding:14px 18px;font-size:14px;color:#64748b;">{{ $enterprise->created_at->format('Y-m-d H:i') }}</td>
                @if ($status === 'rejected')
                    <td style="padding:14px 18px;font-size:14px;color:#ef4444;">{{ $enterprise->audit_remark }}</td>
                @endif
                <td style="padding:14px 18px;">
                    <a href="{{ url('admin/enterprises/' . $enterprise->id) }}" style="color:#3b82f6;text-decoration:none;font-size:14px;">查看详情</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div style="margin-top:16px;">{{ $enterprises->links() }}</div>
@endif
@endsection
