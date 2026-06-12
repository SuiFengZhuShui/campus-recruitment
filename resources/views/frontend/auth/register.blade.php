@extends('frontend.layout')

@section('title', '注册')

@section('content')
<div class="container" style="max-width:500px;margin-top:40px;">
    <div class="card" style="padding:32px;">
        <h2 style="text-align:center;margin-bottom:24px;">用户注册</h2>
        <div id="flash" style="display:none;"></div>

        <div style="display:flex;gap:0;margin-bottom:24px;border-bottom:2px solid #e2e8f0;">
            <button onclick="switchRole('student')" id="tabStudent" style="flex:1;padding:10px;border:none;background:none;font-size:14px;font-weight:600;color:#3b82f6;border-bottom:2px solid #3b82f6;margin-bottom:-2px;cursor:pointer;">学生注册</button>
            <button onclick="switchRole('enterprise')" id="tabEnterprise" style="flex:1;padding:10px;border:none;background:none;font-size:14px;font-weight:500;color:#64748b;cursor:pointer;">企业注册</button>
        </div>

        <!-- Student form -->
        <form id="studentForm" onsubmit="doRegister(event, 'student')">
            @csrf
            <input type="hidden" name="_role" value="student">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div><label style="font-size:13px;color:#475569;">用户名 *</label><input type="text" name="username" required minlength="3" maxlength="30" style="width:100%;padding:9px 12px;border:1px solid #cbd5e1;border-radius:6px;font-size:14px;" placeholder="登录用"></div>
                <div><label style="font-size:13px;color:#475569;">姓名 *</label><input type="text" name="name" required maxlength="50" style="width:100%;padding:9px 12px;border:1px solid #cbd5e1;border-radius:6px;font-size:14px;" placeholder="真实姓名"></div>
                <div><label style="font-size:13px;color:#475569;">手机号 *</label><input type="text" name="phone" required maxlength="20" style="width:100%;padding:9px 12px;border:1px solid #cbd5e1;border-radius:6px;font-size:14px;" placeholder="登录用"></div>
                <div><label style="font-size:13px;color:#475569;">邮箱 *</label><input type="email" name="email" required maxlength="100" style="width:100%;padding:9px 12px;border:1px solid #cbd5e1;border-radius:6px;font-size:14px;" placeholder="登录用"></div>
                <div><label style="font-size:13px;color:#475569;">学号 *</label><input type="text" name="student_no" required maxlength="30" style="width:100%;padding:9px 12px;border:1px solid #cbd5e1;border-radius:6px;font-size:14px;"></div>
                <div><label style="font-size:13px;color:#475569;">班级 *</label><input type="text" name="class_name" required maxlength="100" style="width:100%;padding:9px 12px;border:1px solid #cbd5e1;border-radius:6px;font-size:14px;"></div>
                <div><label style="font-size:13px;color:#475569;">密码 *</label><input type="password" name="password" required minlength="6" maxlength="50" style="width:100%;padding:9px 12px;border:1px solid #cbd5e1;border-radius:6px;font-size:14px;"></div>
                <div><label style="font-size:13px;color:#475569;">验证码 *</label><div style="display:flex;gap:4px;"><input type="text" name="captcha" required maxlength="4" style="flex:1;padding:9px 12px;border:1px solid #cbd5e1;border-radius:6px;font-size:14px;"><img src="/api/auth/captcha" onclick="this.src='/api/auth/captcha?'+Date.now()" style="height:40px;cursor:pointer;border-radius:6px;"></div></div>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%;padding:12px;font-size:16px;justify-content:center;margin-top:16px;">注册学生账号</button>
        </form>

        <!-- Enterprise form -->
        <form id="enterpriseForm" style="display:none;" onsubmit="doRegister(event, 'enterprise')" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="_role" value="enterprise">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div><label style="font-size:13px;color:#475569;">用户名 *</label><input type="text" name="username" required minlength="3" maxlength="30" style="width:100%;padding:9px 12px;border:1px solid #cbd5e1;border-radius:6px;font-size:14px;" placeholder="登录用"></div>
                <div><label style="font-size:13px;color:#475569;">企业全称 *</label><input type="text" name="name" required maxlength="200" style="width:100%;padding:9px 12px;border:1px solid #cbd5e1;border-radius:6px;font-size:14px;"></div>
                <div><label style="font-size:13px;color:#475569;">手机号 *</label><input type="text" name="phone" required maxlength="20" style="width:100%;padding:9px 12px;border:1px solid #cbd5e1;border-radius:6px;font-size:14px;" placeholder="登录用"></div>
                <div><label style="font-size:13px;color:#475569;">邮箱 *</label><input type="email" name="email" required maxlength="100" style="width:100%;padding:9px 12px;border:1px solid #cbd5e1;border-radius:6px;font-size:14px;" placeholder="登录用"></div>
                <div><label style="font-size:13px;color:#475569;">信用代码 *</label><input type="text" name="credit_code" required maxlength="50" style="width:100%;padding:9px 12px;border:1px solid #cbd5e1;border-radius:6px;font-size:14px;"></div>
                <div><label style="font-size:13px;color:#475569;">所属行业 *</label><input type="text" name="industry" required maxlength="50" style="width:100%;padding:9px 12px;border:1px solid #cbd5e1;border-radius:6px;font-size:14px;"></div>
                <div><label style="font-size:13px;color:#475569;">企业规模</label><input type="text" name="scale" maxlength="30" style="width:100%;padding:9px 12px;border:1px solid #cbd5e1;border-radius:6px;font-size:14px;"></div>
                <div><label style="font-size:13px;color:#475569;">联系人 *</label><input type="text" name="contact_name" required maxlength="30" style="width:100%;padding:9px 12px;border:1px solid #cbd5e1;border-radius:6px;font-size:14px;"></div>
                <div><label style="font-size:13px;color:#475569;">联系人电话 *</label><input type="text" name="contact_phone" required maxlength="20" style="width:100%;padding:9px 12px;border:1px solid #cbd5e1;border-radius:6px;font-size:14px;"></div>
                <div><label style="font-size:13px;color:#475569;">密码 *</label><input type="password" name="password" required minlength="6" maxlength="50" style="width:100%;padding:9px 12px;border:1px solid #cbd5e1;border-radius:6px;font-size:14px;"></div>
                <div><label style="font-size:13px;color:#475569;">企业简介</label><textarea name="intro" maxlength="500" style="width:100%;padding:9px 12px;border:1px solid #cbd5e1;border-radius:6px;font-size:14px;resize:vertical;" rows="2"></textarea></div>
                <div><label style="font-size:13px;color:#475569;">营业执照 *</label><input type="file" name="doc_license" required accept=".png,.jpg,.jpeg,.pdf" style="width:100%;font-size:13px;"></div>
                <div><label style="font-size:13px;color:#475569;">身份证 *</label><input type="file" name="doc_id_card" required accept=".png,.jpg,.jpeg,.pdf" style="width:100%;font-size:13px;"></div>
                <div><label style="font-size:13px;color:#475569;">授权书 *</label><input type="file" name="doc_authorization" required accept=".png,.jpg,.jpeg,.pdf" style="width:100%;font-size:13px;"></div>
                <div><label style="font-size:13px;color:#475569;">验证码 *</label><div style="display:flex;gap:4px;"><input type="text" name="captcha" required maxlength="4" style="flex:1;padding:9px 12px;border:1px solid #cbd5e1;border-radius:6px;font-size:14px;"><img id="captchaImg2" src="/api/auth/captcha" onclick="this.src='/api/auth/captcha?'+Date.now()" style="height:40px;cursor:pointer;border-radius:6px;"></div></div>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%;padding:12px;font-size:16px;justify-content:center;margin-top:16px;">注册企业账号</button>
        </form>

        <div style="text-align:center;margin-top:16px;font-size:14px;color:#64748b;">已有账号？<a href="/login" style="color:#3b82f6;">去登录</a></div>
    </div>
</div>

<script>
function switchRole(role) {
    document.getElementById('studentForm').style.display = role === 'student' ? '' : 'none';
    document.getElementById('enterpriseForm').style.display = role === 'enterprise' ? '' : 'none';
    document.getElementById('tabStudent').style.color = role === 'student' ? '#3b82f6' : '#64748b';
    document.getElementById('tabStudent').style.borderBottomColor = role === 'student' ? '#3b82f6' : 'transparent';
    document.getElementById('tabStudent').style.fontWeight = role === 'student' ? '600' : '500';
    document.getElementById('tabEnterprise').style.color = role === 'enterprise' ? '#3b82f6' : '#64748b';
    document.getElementById('tabEnterprise').style.borderBottomColor = role === 'enterprise' ? '#3b82f6' : 'transparent';
    document.getElementById('tabEnterprise').style.fontWeight = role === 'enterprise' ? '600' : '500';
}

async function doRegister(e, role) {
    e.preventDefault();
    clearErrors();
    const fd = new FormData(e.target);
    const url = '/api/auth/register/' + role;
    try {
        const r = await fetch(url, { method:'POST', body:fd, headers:{'X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]').content} });
        const d = await r.json();
        if (d.code === 200) {
            location.href = role === 'enterprise' ? '/enterprise/waiting' : '/';
        } else {
            if (d.errors) showFieldErrors(d.errors);
            else {
                document.getElementById('flash').style.display = 'block';
                document.getElementById('flash').className = 'flash flash-error';
                document.getElementById('flash').textContent = d.message || '注册失败';
            }
            document.querySelectorAll('img[src*="captcha"]').forEach(img => img.src = '/api/auth/captcha?' + Date.now());
        }
    } catch(err) {
        document.getElementById('flash').style.display = 'block';
        document.getElementById('flash').className = 'flash flash-error';
        document.getElementById('flash').textContent = '网络错误';
    }
}

function showFieldErrors(errors) {
    for (const [field, msgs] of Object.entries(errors)) {
        const input = document.querySelector('[name="' + field + '"]');
        if (input) {
            input.style.borderColor = '#ef4444';
            const err = document.createElement('div');
            err.className = 'field-error';
            err.style.cssText = 'color:#ef4444;font-size:12px;margin-top:2px;';
            err.textContent = msgs.join(', ');
            input.parentNode.appendChild(err);
        } else {
            document.getElementById('flash').style.display = 'block';
            document.getElementById('flash').className = 'flash flash-error';
            document.getElementById('flash').textContent = msgs.join(', ');
        }
    }
}

function clearErrors() {
    document.querySelectorAll('.field-error').forEach(e => e.remove());
    document.querySelectorAll('input[style*="border-color"], select[style*="border-color"]').forEach(e => e.style.borderColor = '#cbd5e1');
}
</script>
@endsection
