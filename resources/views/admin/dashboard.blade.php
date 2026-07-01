@extends('layouts.admin')

@section('title', '首页')

@section('content')
<h1 class="page-title">管理后台</h1>

<div class="dash-stats">
    <a href="{{ url('admin/enterprises') }}" class="stat-card card-hover">
        <div class="stat-label">企业总数</div>
        <div class="stat-value">{{ $enterprisePending + $enterpriseApproved + $enterpriseRejected }}</div>
        <div class="stat-detail">
            <span class="status-pending">待审 {{ $enterprisePending }}</span>
            <span class="text-success-dark ml-sm">通过 {{ $enterpriseApproved }}</span>
            <span class="text-danger-dark ml-sm">驳回 {{ $enterpriseRejected }}</span>
        </div>
    </a>
    <a href="{{ url('admin/users?role=student') }}" class="stat-card card-hover">
        <div class="stat-label">学生总数</div>
        <div class="stat-value">{{ $studentCount }}</div>
        <div class="stat-detail text-muted">已注册学生</div>
    </a>
    <div class="stat-card">
        <div class="stat-label">岗位总数</div>
        <div class="stat-value">{{ $jobActive + $jobInactive }}</div>
        <div class="stat-detail">
            <span class="text-success-dark">上架 {{ $jobActive }}</span>
            <span class="text-muted ml-sm">下架 {{ $jobInactive }}</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-label">投递总数</div>
        <div class="stat-value">{{ $applicationCount }}</div>
        <div class="stat-detail text-muted">岗位投递记录</div>
    </div>
</div>

<div class="dash-bottom">
    <div class="card">
        <h3 class="section-title">用户统计</h3>
        <div class="flex gap-2xl">
            <a href="{{ url('admin/users') }}" class="stat-card card-hover">
                <span class="stat-value">{{ $userCount }}</span>
                <div class="stat-label">总用户</div>
            </a>
            <a href="{{ url('admin/users') }}" class="stat-card card-hover">
                <span class="stat-value text-success-dark">{{ $activeUserCount }}</span>
                <div class="stat-label">已激活</div>
            </a>
            <a href="{{ url('admin/users') }}" class="stat-card card-hover">
                <span class="stat-value text-danger">{{ $disabledUserCount }}</span>
                <div class="stat-label">已禁用</div>
            </a>
        </div>
    </div>
    <div class="card">
        <h3 class="section-title">快捷入口</h3>
        <div class="dash-quick-links">
            <a href="{{ url('admin/enterprises?status=pending') }}">→ 待审核企业（{{ $enterprisePending }}）</a>
            <a href="{{ url('admin/users?role=student') }}">→ 学生列表（{{ $studentCount }}）</a>
            <a href="{{ url('admin/users?role=enterprise') }}">→ 企业用户列表</a>
            <a href="{{ url('admin/enterprises?status=rejected') }}" class="text-danger">→ 已驳回企业（{{ $enterpriseRejected }}）</a>
        </div>
    </div>
</div>
@endsection
