@extends('layouts.admin')

@section('title', '新增管理员')

@section('content')
<a href="{{ url('admin/admins') }}" class="btn-back">&larr; 返回管理员列表</a>

<div class="page-header mt-xl">
    <h1>新增学院管理员</h1>
</div>

@if ($errors->any())
    <div class="flash flash-error">
        @foreach ($errors->all() as $error)
            <div>{{ $error }}</div>
        @endforeach
    </div>
@endif

<div class="card" style="max-width:600px;padding:24px;">
    <form method="POST" action="{{ url('admin/admins') }}">
        @csrf
        <div class="form-group">
            <label class="form-label">用户名 *</label>
            <input type="text" name="username" value="{{ old('username') }}" required minlength="3" maxlength="30" class="form-input form-input-lg" placeholder="3-30位字母数字组合">
        </div>
        <div class="form-group">
            <label class="form-label">姓名 *</label>
            <input type="text" name="name" value="{{ old('name') }}" required maxlength="50" class="form-input form-input-lg" placeholder="管理员真实姓名">
        </div>
        <div class="form-group">
            <label class="form-label">手机号 *</label>
            <input type="text" name="phone" value="{{ old('phone') }}" required minlength="11" maxlength="11" class="form-input form-input-lg" placeholder="11位手机号">
        </div>
        <div class="form-group">
            <label class="form-label">邮箱 *</label>
            <input type="email" name="email" value="{{ old('email') }}" required maxlength="100" class="form-input form-input-lg" placeholder="用于接收通知和找回密码">
        </div>
        <div class="form-group">
            <label class="form-label">所属学院 *</label>
            <select name="college_id" required class="form-input form-input-lg">
                <option value="">请选择学院</option>
                @foreach ($colleges as $college)
                    <option value="{{ $college->id }}" {{ old('college_id') == $college->id ? 'selected' : '' }}>{{ $college->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label class="form-label">密码 *</label>
            <input type="password" name="password" required minlength="6" maxlength="50" class="form-input form-input-lg" placeholder="6位以上">
        </div>
        <button type="submit" class="btn btn-primary w-full mt-lg" style="padding:12px;font-size:16px;justify-content:center;">创建管理员</button>
    </form>
</div>
@endsection
