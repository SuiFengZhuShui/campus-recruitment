<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', '校园招聘平台')</title>
    <style>
        * { margin:0;padding:0;box-sizing:border-box; }
        body { font-family:-apple-system,BlinkMacSystemFont,"Segoe UI","PingFang SC","Microsoft YaHei",sans-serif;background:#f5f7fa;color:#333;min-height:100vh; }
        .header { background:#fff;border-bottom:1px solid #e2e8f0;padding:0 24px;height:56px;display:flex;align-items:center;justify-content:space-between; }
        .header .logo { font-size:18px;font-weight:700;color:#1e293b;text-decoration:none; }
        .header .nav { display:flex;gap:20px;align-items:center; }
        .header .nav a { font-size:14px;color:#475569;text-decoration:none; }
        .header .nav a:hover { color:#3b82f6; }
        .header .user-info { font-size:13px;color:#64748b; }
        .container { max-width:1100px;margin:0 auto;padding:24px; }
        .btn { display:inline-flex;align-items:center;padding:8px 20px;border:none;border-radius:8px;font-size:14px;font-weight:500;cursor:pointer;text-decoration:none; }
        .btn-primary { background:linear-gradient(135deg,#3b82f6,#2563eb);color:#fff; }
        .btn-primary:hover { opacity:.9; }
        .btn-outline { border:1px solid #cbd5e1;background:#fff;color:#475569; }
        .btn-outline:hover { border-color:#3b82f6;color:#3b82f6; }
        .btn-danger { background:#ef4444;color:#fff; }
        .btn-success { background:#16a34a;color:#fff; }
        .card { background:#fff;border-radius:10px;box-shadow:0 1px 3px rgba(0,0,0,.06);padding:24px;margin-bottom:16px; }
        .flash { padding:10px 16px;border-radius:8px;font-size:14px;margin-bottom:16px; }
        .flash-success { background:#f0fdf4;color:#166534; }
        .flash-error { background:#fef2f2;color:#991b1b; }
        .tag { display:inline-block;padding:2px 8px;border-radius:10px;font-size:12px; }
        .tag-blue { background:#dbeafe;color:#1e40af; }
        .tag-green { background:#d1fae5;color:#065f46; }
        .tag-yellow { background:#fef3c7;color:#92400e; }
        .tag-red { background:#fee2e2;color:#991b1b; }
        .tag-gray { background:#f1f5f9;color:#64748b; }
    </style>
    @yield('styles')
</head>
<body>
    <header class="header">
        <a href="/" class="logo">校园招聘平台</a>
        <div class="nav">
            <a href="/">岗位搜索</a>
            @if (auth()->check())
                @if (auth()->user()->isEnterprise())
                    <a href="/enterprise/jobs">我的岗位</a>
                @elseif (auth()->user()->isStudent())
                    <a href="/student/applications">我的投递</a>
                    <a href="/student/profile">个人档案</a>
                @endif
                <span class="user-info">{{ auth()->user()->name }}</span>
                <a href="javascript:void(0)" onclick="document.getElementById('logout-form').submit()" style="color:#ef4444;">退出</a>
                <form id="logout-form" method="POST" action="{{ url('api/auth/logout') }}" style="display:none;">
                    @csrf
                </form>
            @else
                <a href="/login">登录</a>
                <a href="/register" class="btn btn-primary">注册</a>
            @endif
        </div>
    </header>
    <main>
        @yield('content')
    </main>
</body>
</html>
