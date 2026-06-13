@extends('layouts.admin')

@section('title', '学院首页')

@section('content')
<h1 style="font-size:20px;font-weight:600;margin-bottom:24px;">学院管理 — {{ auth()->user()->college->name ?? '未分配学院' }}</h1>

<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:24px;">
    <div style="background:#fff;padding:20px;border-radius:10px;box-shadow:0 1px 3px rgba(0,0,0,.06);">
        <div style="font-size:12px;color:#64748b;margin-bottom:4px;">本院学生</div>
        <a href="{{ url('admin/college/students') }}" style="text-decoration:none;color:inherit;"><div style="font-size:28px;font-weight:700;color:#1e293b;">{{ $studentCount }}</div></a>
    </div>
    <div style="background:#fff;padding:20px;border-radius:10px;box-shadow:0 1px 3px rgba(0,0,0,.06);">
        <div style="font-size:12px;color:#64748b;margin-bottom:4px;">对接企业</div>
        <a href="{{ url('admin/college/enterprises') }}" style="text-decoration:none;color:inherit;"><div style="font-size:28px;font-weight:700;color:#1e293b;">{{ $enterpriseCount }}</div></a>
    </div>
    <div style="background:#fff;padding:20px;border-radius:10px;box-shadow:0 1px 3px rgba(0,0,0,.06);">
        <div style="font-size:12px;color:#64748b;margin-bottom:4px;">在招岗位</div>
        <div style="font-size:28px;font-weight:700;color:#1e293b;">{{ $jobCount }}</div>
    </div>
</div>

<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;">
    <div style="background:#fff;padding:20px;border-radius:10px;box-shadow:0 1px 3px rgba(0,0,0,.06);">
        <div style="font-size:12px;color:#64748b;margin-bottom:4px;">投递总数</div>
        <div style="font-size:24px;font-weight:700;color:#3b82f6;">{{ $applicationCount }}</div>
    </div>
    <div style="background:#fff;padding:20px;border-radius:10px;box-shadow:0 1px 3px rgba(0,0,0,.06);">
        <div style="font-size:12px;color:#64748b;margin-bottom:4px;">面试总数</div>
        <div style="font-size:24px;font-weight:700;color:#7c3aed;">{{ $interviewCount }}</div>
    </div>
    <div style="background:#fff;padding:20px;border-radius:10px;box-shadow:0 1px 3px rgba(0,0,0,.06);">
        <div style="font-size:12px;color:#64748b;margin-bottom:4px;">录用总数</div>
        <div style="font-size:24px;font-weight:700;color:#065f46;">{{ $offerCount }}</div>
    </div>
</div>
@endsection
