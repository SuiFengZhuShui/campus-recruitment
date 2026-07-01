@extends('layouts.admin')

@section('title', '编辑企业')

@section('content')
<a href="{{ url('admin/enterprises?status=' . request('status', 'all')) }}" class="btn-back">&larr; 返回企业管理</a>

<div class="page-header mt-xl">
    <h1>编辑「{{ $enterprise->name }}」</h1>
</div>

@if ($errors->any())
    <div class="flash flash-error">
        @foreach ($errors->all() as $error)
            <div>{{ $error }}</div>
        @endforeach
    </div>
@endif

<div class="card" style="max-width:600px;margin:0 auto;padding:24px;">
    <form method="POST" action="{{ url('admin/enterprises/' . $enterprise->id . '?status=' . request('status', 'all')) }}">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label class="form-label">企业名称 *</label>
            <input type="text" name="name" value="{{ old('name', $enterprise->name) }}" required maxlength="200" class="form-input form-input-lg">
        </div>
        <div class="form-group">
            <label class="form-label">信用代码 *</label>
            <input type="text" name="credit_code" value="{{ old('credit_code', $enterprise->credit_code) }}" required maxlength="50" class="form-input form-input-lg">
        </div>
        <div class="form-group">
            <label class="form-label">所属行业 *</label>
            <input type="text" name="industry" value="{{ old('industry', $enterprise->industry) }}" required maxlength="50" class="form-input form-input-lg">
        </div>
        <div class="form-group">
            <label class="form-label">企业规模</label>
            <input type="text" name="scale" value="{{ old('scale', $enterprise->scale) }}" maxlength="30" class="form-input form-input-lg">
        </div>
        <div class="form-group">
            <label class="form-label">联系人 *</label>
            <input type="text" name="contact_name" value="{{ old('contact_name', $enterprise->contact_name) }}" required maxlength="30" class="form-input form-input-lg">
        </div>
        <div class="form-group">
            <label class="form-label">联系电话 *</label>
            <input type="text" name="contact_phone" value="{{ old('contact_phone', $enterprise->contact_phone) }}" required maxlength="11" class="form-input form-input-lg">
        </div>
        <div class="form-group">
            <label class="form-label">所属学院</label>
            <select name="college_id" class="form-input form-input-lg">
                <option value="">未指定</option>
                @foreach ($colleges as $college)
                    <option value="{{ $college->id }}" {{ old('college_id', $enterprise->college_id) == $college->id ? 'selected' : '' }}>{{ $college->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label class="form-label">审核状态</label>
            <select name="status" class="form-input form-input-lg">
                <option value="pending" {{ $enterprise->status === 'pending' ? 'selected' : '' }}>待审核</option>
                <option value="approved" {{ $enterprise->status === 'approved' ? 'selected' : '' }}>已通过</option>
                <option value="rejected" {{ $enterprise->status === 'rejected' ? 'selected' : '' }}>已驳回</option>
            </select>
        </div>
        <div class="form-group">
            <label class="form-label">企业简介</label>
            <textarea name="intro" maxlength="500" rows="3" class="form-input form-input-lg">{{ old('intro', $enterprise->intro) }}</textarea>
        </div>
        <button type="submit" class="btn btn-primary w-full mt-xl" style="padding:12px;font-size:16px;justify-content:center;">保存修改</button>
    </form>
</div>
@endsection
