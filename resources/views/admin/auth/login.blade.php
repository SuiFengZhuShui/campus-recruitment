<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>学校管理员登录 - 校园招聘平台</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", "PingFang SC", "Microsoft YaHei", sans-serif; background: #f0f2f5; display: flex; justify-content: center; align-items: center; min-height: 100vh; }
        .login-card { background: #fff; padding: 40px; border-radius: 12px; box-shadow: 0 4px 24px rgba(0,0,0,.08); width: 400px; max-width: 90vw; }
        .login-card h2 { font-size: 22px; text-align: center; margin-bottom: 8px; color: #1e293b; }
        .login-card .subtitle { text-align: center; color: #94a3b8; font-size: 13px; margin-bottom: 28px; }
        .form-group { margin-bottom: 18px; }
        .form-group label { display: block; font-size: 14px; margin-bottom: 6px; color: #475569; font-weight: 500; }
        .form-group input { width: 100%; padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 15px; transition: border-color .2s; }
        .form-group input:focus { outline: none; border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59,130,246,.1); }
        .btn { width: 100%; padding: 12px; border: none; border-radius: 8px; font-size: 16px; font-weight: 500; cursor: pointer; background: linear-gradient(135deg, #3b82f6, #2563eb); color: #fff; }
        .btn:hover { opacity: .9; }
        .error { color: #ef4444; font-size: 13px; margin-top: 4px; }
        .alert { background: #fef2f2; color: #991b1b; padding: 10px 14px; border-radius: 8px; font-size: 14px; margin-bottom: 18px; }
    </style>
</head>
<body>
    <div class="login-card">
        <h2>校园招聘平台</h2>
        <p class="subtitle">学校管理员登录</p>

        @if ($errors->any())
            <div class="alert">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('admin.login') }}">
            @csrf
            <div class="form-group">
                <label>用户名</label>
                <input type="text" name="username" value="{{ old('username') }}" placeholder="请输入管理员用户名" required autofocus>
            </div>
            <div class="form-group">
                <label>密码</label>
                <input type="password" name="password" placeholder="请输入密码" required>
            </div>
            <button type="submit" class="btn">登 录</button>
        </form>
    </div>
</body>
</html>
