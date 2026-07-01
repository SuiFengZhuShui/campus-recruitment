@extends('layouts.admin')

@section('title', $isCollegeScoped ? '学院首页' : '学校管理')

@section('content')
<h1 class="page-title">
    @if($isCollegeScoped)
        学院管理 — {{ auth()->user()->college->name ?? '' }}
    @else
        学校管理 — 全部学院
    @endif
</h1>

<div class="dash-stats" style="grid-template-columns:repeat(3,1fr);">
    <div class="stat-card">
        <div class="stat-label">{{ $isCollegeScoped ? '本院学生' : '全部学生' }}</div>
        <a href="{{ url('admin/college/students') }}" class="stat-value text-heading" style="text-decoration:none;font-size:28px;display:block;">{{ $studentCount }}</a>
    </div>
    <div class="stat-card">
        <div class="stat-label">{{ $isCollegeScoped ? '对接企业' : '全部企业' }}</div>
        <a href="{{ url('admin/college/enterprises') }}" class="stat-value text-heading" style="text-decoration:none;font-size:28px;display:block;">{{ $enterpriseCount }}</a>
    </div>
    <div class="stat-card">
        <div class="stat-label">{{ $isCollegeScoped ? '本院岗位' : '在招岗位' }}</div>
        <div class="stat-value" style="font-size:28px;">{{ $jobCount }}</div>
    </div>
</div>

<div class="grid-4" style="grid-template-columns:repeat(3,1fr);">
    <div class="stat-card">
        <div class="stat-label">投递总数</div>
        <div class="stat-value text-primary" style="font-size:24px;">{{ $applicationCount }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">面试总数</div>
        <div class="stat-value" style="font-size:24px;color:#7c3aed;">{{ $interviewCount }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">录用总数</div>
        <div class="stat-value text-success-dark" style="font-size:24px;">{{ $offerCount }}</div>
    </div>
</div>
@endsection
