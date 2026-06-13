@extends('frontend.layout')

@section('title', '重置密码')

@section('content')
<div class="container" style="max-width:420px;margin-top:60px;">
    <div class="card" style="padding:32px;">
        <h2 style="text-align:center;margin-bottom:24px;">重置密码</h2>
        <div id="flash" style="display:none;"></div>
        <form id="resetForm" onsubmit="doReset(event)">
            @csrf
            <div style="margin-bottom:16px;">
                <label style="display:block;font-size:14px;color:#475569;margin-bottom:4px;">注册邮箱</label>
                <input type="email" name="email" required style="width:100%;padding:10px 14px;border:1px solid #cbd5e1;border-radius:8px;font-size:15px;" placeholder="请输入注册时使用的邮箱">
            </div>
            <div style="margin-bottom:16px;">
                <label style="display:block;font-size:14px;color:#475569;margin-bottom:4px;">重置令牌</label>
                <input type="text" name="token" required style="width:100%;padding:10px 14px;border:1px solid #cbd5e1;border-radius:8px;font-size:15px;" placeholder="请输入邮件中的重置令牌">
            </div>
            <div style="margin-bottom:16px;">
                <label style="display:block;font-size:14px;color:#475569;margin-bottom:4px;">新密码</label>
                <input type="password" name="password" required minlength="6" style="width:100%;padding:10px 14px;border:1px solid #cbd5e1;border-radius:8px;font-size:15px;" placeholder="请设置新密码（至少6位）">
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%;padding:12px;font-size:16px;justify-content:center;">重置密码</button>
        </form>
        <div style="text-align:center;margin-top:16px;font-size:14px;color:#64748b;">
            <a href="/login" style="color:#3b82f6;">&larr; 返回登录</a>
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
            headers:{'X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]').content}
        });
        const d = await r.json();
        el.style.display = 'block';
        if (d.code === 200) {
            el.className = 'flash flash-success';
            el.innerHTML = d.message + ' <a href="/login" style="color:#3b82f6;">去登录</a>';
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
