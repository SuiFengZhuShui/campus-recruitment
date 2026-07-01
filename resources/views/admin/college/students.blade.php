@extends('layouts.admin')

@section('title', '学生管理')

@section('content')
<a href="{{ url('admin/college') }}" class="btn-back mb-lg">&larr; 返回学院首页</a>

<h1 class="page-title">{{ $isCollegeScoped ? '本院学生列表' : '全部学生列表' }}</h1>

@if($students->isEmpty())
    <div class="card empty-state-card">暂无学生数据</div>
@else
    <div class="table-card" style="overflow:hidden;">
        <table class="table">
            <thead>
                <tr style="background:#f8fafc;">
                    <th>姓名</th>
                    <th>学号</th>
                    @unless($isCollegeScoped)
                    <th>所属学院</th>
                    @endunless
                    <th>班级</th>
                    <th>年级</th>
                    <th>手机</th>
                    <th>邮箱</th>
                </tr>
            </thead>
            <tbody>
                @foreach($students as $student)
                <tr>
                    <td>{{ $student->user->name ?? '-' }}</td>
                    <td>{{ $student->student_no }}</td>
                    @unless($isCollegeScoped)
                    <td>{{ $student->college->name ?? '-' }}</td>
                    @endunless
                    <td>{{ $student->class_name }}</td>
                    <td>{{ $student->grade }}</td>
                    <td>{{ $student->user->phone ?? '-' }}</td>
                    <td>{{ $student->user->email ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="pagination-container">{{ $students->links() }}</div>
@endif
@endsection
