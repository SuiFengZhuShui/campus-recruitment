@extends('layouts.admin')

@section('title', '首页')

@section('content')
<div class="page-header">
    <h1>管理后台</h1>
</div>
<p>欢迎回来，{{ auth()->user()->name }}。</p>
<p style="margin-top:12px;color:#64748b;">使用左侧菜单管理企业审核。</p>
@endsection
