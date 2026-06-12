<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', '管理后台') - 校园招聘平台</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", "PingFang SC", "Microsoft YaHei", sans-serif; background: #f5f7fa; color: #333; }
        .layout { display: flex; min-height: 100vh; }
        .sidebar { width: 220px; background: #1e293b; color: #cbd5e1; flex-shrink: 0; }
        .sidebar-header { padding: 20px; border-bottom: 1px solid #334155; }
        .sidebar-header h2 { font-size: 16px; color: #fff; }
        .sidebar-menu { list-style: none; padding: 12px 0; }
        .sidebar-menu li a { display: flex; align-items: center; gap: 10px; padding: 12px 20px; color: #cbd5e1; text-decoration: none; font-size: 14px; transition: all .15s; }
        .sidebar-menu li a:hover { background: #334155; color: #fff; }
        .sidebar-menu li a.active { background: #3b82f6; color: #fff; }
        .main { flex: 1; display: flex; flex-direction: column; }
        .header { background: #fff; padding: 16px 24px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e2e8f0; }
        .header-user { font-size: 14px; color: #64748b; }
        .header-logout { color: #ef4444; text-decoration: none; font-size: 14px; margin-left: 16px; }
        .content { flex: 1; padding: 24px; }
        .page-header { margin-bottom: 20px; }
        .page-header h1 { font-size: 20px; font-weight: 600; }
    </style>
    @yield('styles')
</head>
<body>
    <div class="layout">
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2>校园招聘平台</h2>
            </div>
            <ul class="sidebar-menu">
                <li><a href="{{ url('admin') }}" class="{{ request()->is('admin') ? 'active' : '' }}">🏠 首页</a></li>
                <li><a href="{{ url('admin/enterprises') }}" class="{{ request()->is('admin/enterprises*') ? 'active' : '' }}">🏢 企业审核</a></li>
                <li><a href="{{ url('admin/users') }}" class="{{ request()->is('admin/users*') ? 'active' : '' }}">👥 用户管理</a></li>
                <li><a href="{{ url('admin/schools') }}" class="{{ request()->is('admin/schools*') ? 'active' : '' }}">🏫 学校管理</a></li>
                <li><a href="{{ url('admin/colleges') }}" class="{{ request()->is('admin/colleges*') ? 'active' : '' }}">📚 学院管理</a></li>
                <li><a href="{{ url('admin/rules') }}" class="{{ request()->is('admin/rules*') ? 'active' : '' }}">🔗 学号规则</a></li>
            </ul>
        </aside>
        <div class="main">
            <header class="header">
                <span>学校管理员</span>
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
