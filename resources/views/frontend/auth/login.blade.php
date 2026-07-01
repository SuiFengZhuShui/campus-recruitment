@extends('frontend.layout')

@section('title', '登录')

@section('content')
<div class="auth-container">
    <a href="/" class="btn-back mb-lg">&larr; 返回首页</a>
    <div class="card" style="padding:32px;">
        <h2 class="auth-title">用户登录</h2>
        <div id="flash" class="hidden"></div>
        <form id="loginForm" onsubmit="doLogin(event)">
            @csrf
            <div class="form-group">
                <label class="form-label">手机号 / 用户名 / 邮箱</label>
                <input type="text" name="account" id="account" required class="form-input form-input-lg" placeholder="请输入">
            </div>
            <div class="form-group">
                <label class="form-label">密码</label>
                <input type="password" name="password" required class="form-input form-input-lg" placeholder="请输入密码">
            </div>
            <div class="form-group">
                <label class="form-label">验证码</label>
                <div class="flex gap-sm">
                    <input type="text" name="captcha" required maxlength="4" class="form-input form-input-lg" style="flex:1;" placeholder="4位验证码">
                    <img id="captchaImg" src="/api/auth/captcha" onclick="this.src='/api/auth/captcha?'+Date.now()" style="height:42px;cursor:pointer;border-radius:8px;" title="点击刷新">
                </div>
            </div>
            <button type="submit" class="btn btn-primary w-full" style="padding:12px;font-size:16px;justify-content:center;">登 录</button>
        </form>
        <div class="auth-link">
            没有账号？<a href="/register">立即注册</a>
            <span style="margin:0 8px;">|</span>
            <a href="/forgot-password">忘记密码</a>
        </div>
    </div>
</div>

<script>
async function doLogin(e) {
    e.preventDefault();
    clearErrors();
    const fd = new FormData(e.target);
    try {
        const r = await fetch('/api/auth/login', { method:'POST', body:fd, headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest','X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]').content} });
        const d = await r.json();
        if (d.code === 200) {
            const role = d.data.role;
            if (role === 'enterprise') {
                location.href = d.data.enterprise_status === 'approved' ? '/enterprise/dashboard' : '/enterprise/waiting';
            } else if (role === 'school' || role === 'college') {
                location.href = '/admin';
            } else if (role === 'student') {
                location.href = '/';
            } else {
                location.href = '/';
            }
        } else {
            if (d.errors) showFieldErrors(d.errors);
            else showFlash(d.message || '登录失败', 'error');
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

function showFieldErrors(errors) {
    for (const [field, msgs] of Object.entries(errors)) {
        const input = document.querySelector('[name="' + field + '"]');
        if (input) {
            input.style.borderColor = '#ef4444';
            const err = document.createElement('div');
            err.className = 'field-error';
            err.style.cssText = 'color:#ef4444;font-size:12px;margin-top:4px;';
            err.textContent = msgs.join(', ');
            // captcha: append to form-group so it sits below the img, not in flex row
            if (field === 'captcha') {
                input.closest('.form-group').appendChild(err);
            } else {
                input.parentNode.appendChild(err);
            }
        } else {
            showFlash(msgs.join(', '), 'error');
        }
    }
}

function clearErrors() {
    document.querySelectorAll('.field-error').forEach(e => e.remove());
    document.querySelectorAll('input[style*="border-color"]').forEach(e => e.style.borderColor = '#cbd5e1');
}
</script>
@endsection
