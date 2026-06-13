<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>校园招聘平台</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", "PingFang SC", "Microsoft YaHei", sans-serif; background: linear-gradient(135deg, #0f172a, #1e3a5f); min-height:100vh; display:flex; flex-direction:column; align-items:center; justify-content:center; color:#fff; text-align:center; padding:24px; }
        h1 { font-size: clamp(28px, 5vw, 48px); margin-bottom: 12px; }
        p { font-size: 16px; color: #94a3b8; margin-bottom: 32px; max-width: 480px; }
        .actions { display: flex; gap: 12px; flex-wrap: wrap; justify-content: center; }
        .btn { display: inline-block; padding: 12px 28px; border-radius: 10px; font-size: 15px; font-weight: 500; text-decoration: none; transition: all .15s; }
        .btn-primary { background: #3b82f6; color: #fff; }
        .btn-primary:hover { background: #2563eb; }
        .btn-outline { border: 1px solid rgba(255,255,255,.3); color: #fff; }
        .btn-outline:hover { background: rgba(255,255,255,.1); }
        .footer { margin-top: 48px; font-size: 13px; color: #64748b; }
    </style>
</head>
<body>
    <h1>校园招聘平台</h1>
    <p>连接学生与企业，一站式完成岗位发布、简历投递、面试安排、录用管理。</p>
    <div class="actions">
        <a href="/login" class="btn btn-primary">学生 / 企业登录</a>
        <a href="/register" class="btn btn-outline">注册账号</a>
        <a href="/admin/login" class="btn btn-outline">管理员入口</a>
    </div>
    <div class="footer">校园招聘平台 &copy; {{ date('Y') }}</div>
</body>
</html>
