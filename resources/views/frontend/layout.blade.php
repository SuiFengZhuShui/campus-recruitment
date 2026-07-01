<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="user-authenticated" content="{{ auth()->check() ? '1' : '0' }}">
    <title>@yield('title', config('school.platform_name') . ' — ' . config('school.short_name'))</title>
    <link rel="stylesheet" href="{{ url('css/tokens.css') }}">
    <link rel="stylesheet" href="{{ url('css/reset.css') }}">
    <link rel="stylesheet" href="{{ url('css/components.css') }}">
    <link rel="stylesheet" href="{{ url('css/layout-frontend.css') }}">
    <link rel="stylesheet" href="{{ url('css/pages/frontend.css') }}">
    @yield('styles')
</head>
<body>
    <header class="header-main">
        <a href="/" class="logo">
            <img src="{{ url(config('school.logo')) }}" alt="校徽" class="logo-img">
            {{ config('school.short_name') }} — {{ config('school.platform_name') }}
        </a>
        <div class="nav">
            <a href="/enterprises">合作企业</a>
            @if (auth()->check())
                @if (auth()->user()->isEnterprise())
                    <a href="/enterprise/dashboard">企业后台</a>
                    <a href="/enterprise/jobs">我的岗位</a>
                @elseif (auth()->user()->isStudent())
                    <a href="/student/dashboard">个人中心</a>
                    <a href="/student/applications">我的投递</a>
                @endif
                <span class="user-info">{{ auth()->user()->name }}</span>
                <a href="javascript:void(0)" onclick="doLogout()" class="text-danger">退出</a>
                <form id="logout-form" class="hidden">
                    @csrf
                </form>
            @else
                <a href="/login">登录</a>
                <a href="/register">注册</a>
            @endif
        </div>
    </header>
    <main>
        @yield('content')
    </main>
    <footer class="footer-main">
        <div class="footer-bottom">
            {{ config('school.copyright') }}@if(config('school.address'))<span class="footer-divider">|</span>{{ config('school.address') }}@endif @if(config('school.icp'))<span class="footer-divider">|</span>{{ config('school.icp') }}@endif
        </div>
    </footer>
    <script>
    function goBack(fallback) {
        try {
            var ref = new URL(document.referrer);
            var skip = /^\/(login|register|forgot-password|reset-password|student\/dashboard|student\/resume|student\/applications|student\/interviews|student\/offers|student\/profile|enterprise\/dashboard|enterprise\/docs|enterprise\/jobs|enterprise\/resumes|enterprise\/profile|enterprise\/waiting)/.test(ref.pathname);
            if (!skip && ref.hostname === location.hostname && ref.pathname !== location.pathname) {
                location.href = ref.pathname + ref.search;
                return false;
            }
        } catch(e) {}
        location.href = fallback;
        return false;
    }

    async function doLogout() {
        try {
            await fetch('/api/auth/logout', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                }
            });
        } catch(e) {}
        location.href = '/';
    }

    function showToast(msg) {
        var t = document.createElement('div');
        t.textContent = msg;
        t.style.cssText = 'position:fixed;top:20px;left:50%;transform:translateX(-50%);z-index:9999;background:#fef2f2;color:#991b1b;border:1px solid #fecaca;padding:12px 24px;border-radius:8px;font-size:14px;font-weight:500;box-shadow:0 4px 12px rgba(0,0,0,.15);animation:toastIn .3s ease;white-space:nowrap;';
        document.body.appendChild(t);
        setTimeout(function(){ t.style.opacity = '0'; t.style.transition = 'opacity .3s'; setTimeout(function(){ t.remove(); }, 300); }, 1500);
    }
    </script>
    <style>
    @keyframes toastIn { from { opacity:0; transform:translateX(-50%) translateY(-10px); } to { opacity:1; transform:translateX(-50%) translateY(0); } }
    </style>
</body>
</html>
