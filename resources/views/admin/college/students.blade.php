@extends('layouts.admin')

@section('title', '本院学生')

@section('content')
<a href="{{ url('admin/college') }}" style="display:inline-flex;align-items:center;gap:4px;padding:8px 18px;background:linear-gradient(135deg,#c7915c,#d4a574);color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:500;text-decoration:none;margin-bottom:16px;">&larr; 返回学院首页</a>

<h1 style="font-size:20px;font-weight:600;margin-bottom:20px;">本院学生列表</h1>

@if($students->isEmpty())
    <div style="background:#fff;padding:60px;text-align:center;color:#94a3b8;border-radius:10px;">暂无学生数据</div>
@else
    <div style="background:#fff;border-radius:10px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,.06);">
        <table style="width:100%;border-collapse:collapse;font-size:14px;">
            <thead>
                <tr style="background:#f8fafc;text-align:left;">
                    <th style="padding:12px 16px;color:#64748b;font-weight:500;">姓名</th>
                    <th style="padding:12px 16px;color:#64748b;font-weight:500;">学号</th>
                    <th style="padding:12px 16px;color:#64748b;font-weight:500;">班级</th>
                    <th style="padding:12px 16px;color:#64748b;font-weight:500;">年级</th>
                    <th style="padding:12px 16px;color:#64748b;font-weight:500;">手机</th>
                    <th style="padding:12px 16px;color:#64748b;font-weight:500;">邮箱</th>
                </tr>
            </thead>
            <tbody>
                @foreach($students as $student)
                <tr style="border-top:1px solid #f1f5f9;">
                    <td style="padding:12px 16px;">{{ $student->user->name ?? '-' }}</td>
                    <td style="padding:12px 16px;">{{ $student->student_no }}</td>
                    <td style="padding:12px 16px;">{{ $student->class_name }}</td>
                    <td style="padding:12px 16px;">{{ $student->grade }}</td>
                    <td style="padding:12px 16px;">{{ $student->user->phone ?? '-' }}</td>
                    <td style="padding:12px 16px;">{{ $student->user->email ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div style="margin-top:16px;">{{ $students->links() }}</div>
@endif
@endsection
