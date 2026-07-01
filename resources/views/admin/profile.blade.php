@extends('layouts.admin')

@section('title', '修改密码')

@section('content')
<h1 class="page-title">修改密码</h1>

@if (session('success'))
    <div class="flash flash-success">{{ session('success') }}</div>
@endif
@if ($errors->any())
    <div class="flash flash-error">
        @foreach ($errors->all() as $error)
            <div>{{ $error }}</div>
        @endforeach
    </div>
@endif

<div class="card" style="max-width:500px;margin:0 auto;padding:24px;">
    <form method="POST" action="{{ url('admin/profile/password') }}">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label class="form-label">当前密码</label>
            <input type="password" name="current_password" required class="form-input form-input-lg" placeholder="请输入当前密码">
        </div>
        <br>
        <div class="form-group">
            <label class="form-label">新密码</label>
            <input type="password" name="password" required minlength="6" maxlength="50" class="form-input form-input-lg" placeholder="6位以上">
        </div>
        <br>
        <div class="form-group">
            <label class="form-label">确认新密码</label>
            <input type="password" name="password_confirmation" required minlength="6" maxlength="50" class="form-input form-input-lg" placeholder="请再次输入新密码">
        </div>
        <button type="submit" class="btn btn-primary w-full mt-xl" style="padding:12px;font-size:16px;justify-content:center;">修改密码</button>
    </form>
</div>
@endsection
