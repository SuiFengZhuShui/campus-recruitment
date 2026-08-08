<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', '管理后台') - {{ config('school.short_name') }}</title>
    <link rel="stylesheet" href="{{ url('css/tokens.css') }}">
    @if(session('theme'))
    <link rel="stylesheet" href="{{ url('css/themes/theme-' . session('theme') . '.css') }}">
    @endif
    <link rel="stylesheet" href="{{ url('css/reset.css') }}">
    <link rel="stylesheet" href="{{ url('css/components.css') }}">
    <link rel="stylesheet" href="{{ url('css/layout-admin.css') }}">
    <link rel="stylesheet" href="{{ url('css/pages/admin.css') }}">
    @yield('styles')
</head>
<body>
    <div class="layout">
        <aside class="sidebar">
            <div class="sidebar-header">
                <span class="sidebar-school">管理后台</span>
            </div>
            @php $role = auth()->user()->role ?? ''; @endphp
            <ul class="sidebar-menu">
                @if($role === 'school')
                <li><a href="{{ url('admin') }}" class="{{ request()->is('admin') ? 'active' : '' }}">🏠 首页</a></li>
                <li><a href="{{ url('admin/enterprises') }}" class="{{ request()->is('admin/enterprises*') ? 'active' : '' }}">🏢 企业管理</a></li>
                <li><a href="{{ url('admin/users') }}" class="{{ request()->is('admin/users*') ? 'active' : '' }}">👥 用户管理</a></li>
                <li><a href="{{ url('admin/colleges') }}" class="{{ request()->is('admin/colleges*') ? 'active' : '' }}">📚 学院管理</a></li>
                <li><a href="{{ url('admin/admins') }}" class="{{ request()->is('admin/admins*') ? 'active' : '' }}">👤 管理员账号</a></li>
                <li><a href="{{ url('admin/jobs') }}" class="{{ request()->is('admin/jobs*') ? 'active' : '' }}">💼 岗位管理</a></li>
                @endif
                <li><a href="{{ url('admin/rules') }}" class="{{ request()->is('admin/rules*') ? 'active' : '' }}">🔗 学号规则</a></li>
                <li><a href="{{ url('admin/college') }}" class="{{ request()->is('admin/college') ? 'active' : '' }}">📊 {{ $role === 'college' ? '本院数据' : '全校数据' }}</a></li>
            </ul>
            <ul class="sidebar-menu" style="margin-top:auto;border-top:1px solid var(--color-border);padding-top:12px;">
                <li>
                    <form method="POST" action="{{ route('admin.theme.switch') }}" style="display:flex;align-items:center;gap:6px;padding:4px 0;">
                        @csrf
                        <span>🎨</span>
                        <select name="theme" onchange="this.form.submit()" style="flex:1;padding:4px 6px;font-size:12px;background:var(--color-sidebar);color:var(--color-sidebar-text);border:1px solid var(--color-sidebar-text);border-radius:4px;cursor:pointer;">
                            @php $themes = ['classic-red'=>'经典红','dark-purple'=>'暗夜紫','deep-space'=>'深空蓝','fresh-green'=>'清新绿','pure-white'=>'纯净白','warm-campus'=>'暖校园']; @endphp
                            <option value="">🏫 品牌默认</option>
                            @foreach($themes as $key => $label)
                                <option value="{{ $key }}" {{ session('theme') === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </form>
                </li>
                <li><a href="{{ url('admin/profile') }}" class="{{ request()->is('admin/profile*') ? 'active' : '' }}">🔒 修改密码</a></li>
            </ul>
        </aside>
        <div class="main">
            <header class="header">
                <span>{{ $role === 'college' ? '学院管理员' : '学校管理员' }}</span>
                <div>
                    <span class="header-user">{{ auth()->user()->name }}</span>
                    <a href="{{ route('admin.logout') }}" class="header-logout" onclick="event.preventDefault();document.getElementById('logout-form').submit();">退出</a>
                    <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display:none;">
                        @csrf
                    </form>
                </div>
            </header>
            <div class="content">
                @yield('content')
            </div>
        </div>
    </div>
</body>
</html>
