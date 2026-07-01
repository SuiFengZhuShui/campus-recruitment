@extends('layouts.admin')

@section('title', '编辑用户')

@section('content')
<a href="{{ url('admin/users') }}" class="btn-back">&larr; 返回用户管理</a>

<div class="card mt-xl" style="max-width:480px;padding:24px;">
    <h2 class="section-title">编辑用户 #{{ $user->id }}</h2>

    @if ($errors->any())
        <div class="flash flash-error">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ url('admin/users/' . $user->id) }}">
        @csrf
        <div class="form-group">
            <label class="form-label">姓名</label>
            <input type="text" name="name" required value="{{ old('name', $user->name) }}" class="form-input">
        </div>
        <div class="form-group">
            <label class="form-label">用户名</label>
            <input type="text" name="username" required value="{{ old('username', $user->username) }}" class="form-input">
        </div>
        <div class="form-group">
            <label class="form-label">手机号</label>
            <input type="text" name="phone" required value="{{ old('phone', $user->phone) }}" class="form-input">
        </div>
        <div class="form-group">
            <label class="form-label">邮箱</label>
            <input type="email" name="email" required value="{{ old('email', $user->email) }}" class="form-input">
        </div>
        <div class="form-group">
            <label class="form-label">状态</label>
            <select name="status" class="form-input">
                <option value="active" {{ $user->status === 'active' ? 'selected' : '' }}>正常</option>
                <option value="disabled" {{ $user->status === 'disabled' ? 'selected' : '' }}>禁用</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary w-full" style="justify-content:center;">保存</button>
    </form>
</div>
@endsection
