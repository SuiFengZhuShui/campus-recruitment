@extends('frontend.layout')

@section('title', '重置密码')

@section('content')
<div class="auth-container">
    <div class="card" style="padding:32px;">
        <h2 class="auth-title">重置密码</h2>
        <div id="flash" class="hidden"></div>
        <form id="resetForm" onsubmit="doReset(event)">
            @csrf
            <div class="form-group">
                <label class="form-label">注册邮箱</label>
                <input type="email" name="email" required class="form-input form-input-lg" placeholder="请输入注册时使用的邮箱">
            </div>
            <div class="form-group">
                <label class="form-label">重置令牌</label>
                <input type="text" name="token" required class="form-input form-input-lg" placeholder="请输入邮件中的重置令牌">
            </div>
            <div class="form-group">
                <label class="form-label">新密码</label>
                <input type="password" name="password" required minlength="6" class="form-input form-input-lg" placeholder="请设置新密码（至少6位）">
            </div>
            <button type="submit" class="btn btn-primary w-full" style="padding:12px;font-size:16px;justify-content:center;">重置密码</button>
        </form>
        <div class="auth-link">
            <a href="/login">&larr; 返回登录</a>
        </div>
    </div>
</div>

<script>
async function doReset(e) {
    e.preventDefault();
    const el = document.getElementById('flash');
    const fd = new FormData(e.target);
    try {
        const r = await fetch('/api/auth/reset-password', {
            method:'POST', body:fd,
            headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest','X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]').content}
        });
        const d = await r.json();
        el.style.display = 'block';
        if (d.code === 200) {
            el.className = 'flash flash-success';
            el.innerHTML = d.message + ' <a href="/login" class="text-primary">去登录</a>';
        } else {
            el.className = 'flash flash-error';
            el.textContent = d.message || '重置失败';
            if (d.errors) {
                for (const msgs of Object.values(d.errors)) {
                    el.textContent += ' ' + msgs.join(', ');
                }
            }
        }
    } catch(err) {
        el.style.display = 'block';
        el.className = 'flash flash-error';
        el.textContent = '网络错误';
    }
}
</script>
@endsection
