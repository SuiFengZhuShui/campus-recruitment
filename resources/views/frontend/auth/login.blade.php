@extends('frontend.layout')

@section('title', '登录')

@section('content')
<div class="container" style="max-width:420px;margin-top:60px;">
    <div class="card" style="padding:32px;">
        <h2 style="text-align:center;margin-bottom:24px;">用户登录</h2>
        <div id="flash" style="display:none;"></div>
        <form id="loginForm" onsubmit="doLogin(event)">
            @csrf
            <div style="margin-bottom:16px;">
                <label style="display:block;font-size:14px;color:#475569;margin-bottom:4px;">手机号 / 用户名 / 邮箱</label>
                <input type="text" name="account" id="account" required style="width:100%;padding:10px 14px;border:1px solid #cbd5e1;border-radius:8px;font-size:15px;" placeholder="请输入">
            </div>
            <div style="margin-bottom:16px;">
                <label style="display:block;font-size:14px;color:#475569;margin-bottom:4px;">密码</label>
                <input type="password" name="password" required style="width:100%;padding:10px 14px;border:1px solid #cbd5e1;border-radius:8px;font-size:15px;" placeholder="请输入密码">
            </div>
            <div style="margin-bottom:16px;">
                <label style="display:block;font-size:14px;color:#475569;margin-bottom:4px;">验证码</label>
                <div style="display:flex;gap:8px;">
                    <input type="text" name="captcha" required maxlength="4" style="flex:1;padding:10px 14px;border:1px solid #cbd5e1;border-radius:8px;font-size:15px;" placeholder="4位验证码">
                    <img id="captchaImg" src="/api/auth/captcha" onclick="this.src='/api/auth/captcha?'+Date.now()" style="height:42px;cursor:pointer;border-radius:8px;" title="点击刷新">
                </div>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%;padding:12px;font-size:16px;justify-content:center;">登 录</button>
        </form>
        <div style="text-align:center;margin-top:16px;font-size:14px;color:#64748b;">
            没有账号？<a href="/register" style="color:#3b82f6;">立即注册</a>
        </div>
    </div>
</div>

<script>
async function doLogin(e) {
    e.preventDefault();
    const fd = new FormData(e.target);
    try {
        const r = await fetch('/api/auth/login', { method:'POST', body:fd, headers:{'X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]').content} });
        const d = await r.json();
        if (d.code === 200) {
            const role = d.data.role;
            if (role === 'enterprise') location.href = '/enterprise/jobs';
            else if (role === 'student') location.href = '/';
            else location.href = '/';
        } else {
            showFlash(d.message || '登录失败', 'error');
            document.getElementById('captchaImg').src = '/api/auth/captcha?' + Date.now();
        }
    } catch(err) {
        showFlash('网络错误', 'error');
    }
}
function showFlash(msg, type) {
    const el = document.getElementById('flash');
    el.style.display = 'block';
    el.className = 'flash flash-' + (type === 'error' ? 'error' : 'success');
    el.textContent = msg;
}
</script>
@endsection
