@extends('layouts.admin')

@section('title', '首页')

@section('content')
<h1 style="font-size:20px;font-weight:600;margin-bottom:24px;">管理后台</h1>

<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:24px;">
    <a href="{{ url('admin/enterprises') }}" style="text-decoration:none;background:#fff;padding:20px;border-radius:10px;box-shadow:0 1px 3px rgba(0,0,0,.06);transition:box-shadow .15s;" onmouseover="this.style.boxShadow='0 4px 12px rgba(0,0,0,.1)'" onmouseout="this.style.boxShadow='0 1px 3px rgba(0,0,0,.06)'">
        <div style="font-size:12px;color:#64748b;margin-bottom:4px;">企业总数</div>
        <div style="font-size:24px;font-weight:700;color:#1e293b;">{{ $enterprisePending + $enterpriseApproved + $enterpriseRejected }}</div>
        <div style="font-size:12px;margin-top:4px;">
            <span style="color:#92400e;">待审 {{ $enterprisePending }}</span>
            <span style="color:#065f46;margin-left:8px;">通过 {{ $enterpriseApproved }}</span>
            <span style="color:#991b1b;margin-left:8px;">驳回 {{ $enterpriseRejected }}</span>
        </div>
    </a>
    <a href="{{ url('admin/users?role=student') }}" style="text-decoration:none;background:#fff;padding:20px;border-radius:10px;box-shadow:0 1px 3px rgba(0,0,0,.06);transition:box-shadow .15s;" onmouseover="this.style.boxShadow='0 4px 12px rgba(0,0,0,.1)'" onmouseout="this.style.boxShadow='0 1px 3px rgba(0,0,0,.06)'">
        <div style="font-size:12px;color:#64748b;margin-bottom:4px;">学生总数</div>
        <div style="font-size:24px;font-weight:700;color:#1e293b;">{{ $studentCount }}</div>
        <div style="font-size:12px;color:#64748b;margin-top:4px;">已注册学生</div>
    </a>
    <div style="background:#fff;padding:20px;border-radius:10px;box-shadow:0 1px 3px rgba(0,0,0,.06);">
        <div style="font-size:12px;color:#64748b;margin-bottom:4px;">岗位总数</div>
        <div style="font-size:24px;font-weight:700;color:#1e293b;">{{ $jobActive + $jobInactive }}</div>
        <div style="font-size:12px;margin-top:4px;">
            <span style="color:#065f46;">上架 {{ $jobActive }}</span>
            <span style="color:#64748b;margin-left:8px;">下架 {{ $jobInactive }}</span>
        </div>
    </div>
    <div style="background:#fff;padding:20px;border-radius:10px;box-shadow:0 1px 3px rgba(0,0,0,.06);">
        <div style="font-size:12px;color:#64748b;margin-bottom:4px;">投递总数</div>
        <div style="font-size:24px;font-weight:700;color:#1e293b;">{{ $applicationCount }}</div>
        <div style="font-size:12px;color:#64748b;margin-top:4px;">岗位投递记录</div>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
    <div style="background:#fff;padding:20px;border-radius:10px;box-shadow:0 1px 3px rgba(0,0,0,.06);">
        <h3 style="font-size:15px;font-weight:600;color:#1e293b;margin-bottom:12px;">用户统计</h3>
        <div style="display:flex;gap:24px;">
            <a href="{{ url('admin/users') }}" style="text-decoration:none;color:inherit;">
                <span style="font-size:20px;font-weight:600;color:#1e293b;">{{ $userCount }}</span>
                <div style="font-size:12px;color:#64748b;">总用户</div>
            </a>
            <a href="{{ url('admin/users') }}" style="text-decoration:none;color:inherit;">
                <span style="font-size:20px;font-weight:600;color:#065f46;">{{ $activeUserCount }}</span>
                <div style="font-size:12px;color:#64748b;">已激活</div>
            </a>
            <div>
                <span style="font-size:20px;font-weight:600;color:#ef4444;">{{ $disabledUserCount }}</span>
                <div style="font-size:12px;color:#64748b;">已禁用</div>
            </div>
        </div>
    </div>
    <div style="background:#fff;padding:20px;border-radius:10px;box-shadow:0 1px 3px rgba(0,0,0,.06);">
        <h3 style="font-size:15px;font-weight:600;color:#1e293b;margin-bottom:12px;">快捷入口</h3>
        <div style="display:flex;flex-direction:column;gap:8px;font-size:14px;">
            <a href="{{ url('admin/enterprises?status=pending') }}" style="color:#3b82f6;text-decoration:none;">→ 待审核企业（{{ $enterprisePending }}）</a>
            <a href="{{ url('admin/users?role=student') }}" style="color:#3b82f6;text-decoration:none;">→ 学生列表（{{ $studentCount }}）</a>
            <a href="{{ url('admin/users?role=enterprise') }}" style="color:#3b82f6;text-decoration:none;">→ 企业用户列表</a>
            <a href="{{ url('admin/enterprises?status=rejected') }}" style="color:#ef4444;text-decoration:none;">→ 已驳回企业（{{ $enterpriseRejected }}）</a>
        </div>
    </div>
</div>
@endsection
