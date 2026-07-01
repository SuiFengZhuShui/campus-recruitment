@extends('frontend.layout')

@section('title', '忘记密码')

@section('content')
<div class="auth-container">
    <div class="card" style="padding:32px;">
        <h2 class="auth-title">忘记密码</h2>
        <div id="flash" class="hidden"></div>
        <form id="forgotForm" onsubmit="doForgot(event)">
            @csrf
            <div class="form-group">
                <label class="form-label">注册邮箱</label>
                <input type="email" name="email" required class="form-input form-input-lg" placeholder="请输入注册时使用的邮箱">
            </div>
            <button type="submit" class="btn btn-primary w-full" style="padding:12px;font-size:16px;justify-content:center;">发送重置链接</button>
        </form>
        <div class="auth-link">
            <a href="/login">&larr; 返回登录</a>
        </div>
    </div>
</div>

<script>
async function doForgot(e) {
    e.preventDefault();
    const el = document.getElementById('flash');
    const fd = new FormData(e.target);
    try {
        const r = await fetch('/api/auth/forgot-password', {
            method:'POST', body:fd,
            headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest','X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]').content}
        });
        const d = await r.json();
        el.style.display = 'block';
        if (d.code === 200) {
            el.className = 'flash flash-success';
            el.innerHTML = d.message + '<br><a href="/reset-password" class="text-primary text-sm">前往重置密码页面</a>';
        } else if (d.data && d.data.email_not_found) {
            el.className = 'flash flash-error';
            el.innerHTML = '邮箱未注册，<a href="/register" class="text-primary">前往注册</a>';
        } else {
            el.className = 'flash flash-error';
            el.textContent = d.message;
        }
    } catch(err) {
        el.style.display = 'block';
        el.className = 'flash flash-error';
        el.textContent = '网络错误';
    }
}
</script>
@endsection
