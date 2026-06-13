@extends('frontend.layout')

@section('title', '忘记密码')

@section('content')
<div class="container" style="max-width:420px;margin-top:60px;">
    <div class="card" style="padding:32px;">
        <h2 style="text-align:center;margin-bottom:24px;">忘记密码</h2>
        <div id="flash" style="display:none;"></div>
        <form id="forgotForm" onsubmit="doForgot(event)">
            @csrf
            <div style="margin-bottom:16px;">
                <label style="display:block;font-size:14px;color:#475569;margin-bottom:4px;">注册邮箱</label>
                <input type="email" name="email" required style="width:100%;padding:10px 14px;border:1px solid #cbd5e1;border-radius:8px;font-size:15px;" placeholder="请输入注册时使用的邮箱">
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%;padding:12px;font-size:16px;justify-content:center;">发送重置链接</button>
        </form>
        <div style="text-align:center;margin-top:16px;font-size:14px;color:#64748b;">
            <a href="/login" style="color:#3b82f6;">&larr; 返回登录</a>
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
            headers:{'X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]').content}
        });
        const d = await r.json();
        el.style.display = 'block';
        el.className = 'flash flash-' + (d.code === 200 ? 'success' : 'error');
        el.textContent = d.message;
        if (d.code === 200) {
            el.innerHTML += '<br><a href="/reset-password" style="color:#3b82f6;font-size:13px;">前往重置密码页面</a>';
        }
    } catch(err) {
        el.style.display = 'block';
        el.className = 'flash flash-error';
        el.textContent = '网络错误';
    }
}
</script>
@endsection
