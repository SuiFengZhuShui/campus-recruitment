@extends('frontend.layout')

@section('title', '注册')

@section('content')
<div class="container" style="max-width:500px;margin-top:40px;">
    <a href="/" class="btn-back mb-lg">&larr; 返回首页</a>
    <div class="card" style="padding:32px;">
        <h2 class="auth-title">用户注册</h2>
        <div id="flash" class="hidden"></div>

        <!-- Student form -->
        <form id="studentForm" onsubmit="doRegister(event, 'student')">
            @csrf
            <input type="hidden" name="_role" value="student">
            <div class="register-grid">
                <div>
                    <label class="form-label">用户名 *</label>
                    <input type="text" name="username" required minlength="3" maxlength="30" class="form-input form-input-lg" placeholder="3-30位字母数字组合，用于登录，不可修改">
                </div>
                <div>
                    <label class="form-label">姓名 *</label>
                    <input type="text" name="name" required maxlength="50" class="form-input form-input-lg" placeholder="真实姓名，企业将以此确认你的身份">
                </div>
                <div>
                    <label class="form-label">手机号 *</label>
                    <input type="text" name="phone" required maxlength="11" class="form-input form-input-lg" placeholder="11位手机号，用于接收投递进度、面试通知和找回密码">
                </div>
                <div>
                    <label class="form-label">邮箱 *</label>
                    <input type="email" name="email" required maxlength="100" class="form-input form-input-lg" placeholder="用于接收录用通知和重置密码">
                </div>
                <div>
                    <label class="form-label">学号 *</label>
                    <input type="text" name="student_no" required maxlength="30" class="form-input form-input-lg" placeholder="学校系统学号，用于验证学生身份">
                </div>
                <div>
                    <label class="form-label">班级 *</label>
                    <input type="text" name="class_name" required maxlength="100" class="form-input form-input-lg" placeholder="例：2024计算机科学与技术1班">
                </div>
                <div>
                    <label class="form-label">密码 *</label>
                    <input type="password" name="password" required minlength="6" maxlength="50" class="form-input form-input-lg" placeholder="6位以上，大小写字母+数字安全性更高">
                </div>
                <div>
                    <label class="form-label">确认密码 *</label>
                    <input type="password" name="password_confirmation" required minlength="6" maxlength="50" class="form-input form-input-lg" placeholder="请再次输入密码">
                </div>
                <div>
                    <label class="form-label">验证码 *</label>
                    <div class="flex gap-xs"><input type="text" name="captcha" required maxlength="4" class="form-input form-input-lg" placeholder="4位验证码，点击图片刷新" style="flex:1;"><img src="/api/auth/captcha" onclick="this.src='/api/auth/captcha?'+Date.now()" style="height:40px;cursor:pointer;border-radius:6px;" title="点击刷新"></div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary w-full mt-lg" style="padding:12px;font-size:16px;justify-content:center;">注册学生账号</button>
        </form>

        <!-- Enterprise form -->
        <form id="enterpriseForm" style="display:none;" onsubmit="doRegister(event, 'enterprise')" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="_role" value="enterprise">
            <div class="register-grid">
                <div>
                    <label class="form-label">用户名 *</label>
                    <input type="text" name="username" required minlength="3" maxlength="30" class="form-input form-input-lg" placeholder="3-30位字母数字组合，用于登录，不可修改">
                </div>
                <div>
                    <label class="form-label">企业全称 *</label>
                    <input type="text" name="name" required maxlength="200" class="form-input form-input-lg" placeholder="须与营业执照一致，学校将进行资质审核">
                </div>
                <div>
                    <label class="form-label">手机号 *</label>
                    <input type="text" name="phone" required maxlength="11" class="form-input form-input-lg" placeholder="11位手机号，用于接收审核结果和找回密码">
                </div>
                <div>
                    <label class="form-label">邮箱 *</label>
                    <input type="email" name="email" required maxlength="100" class="form-input form-input-lg" placeholder="审核结果、学生投递通知将发送至此邮箱">
                </div>
                <div>
                    <label class="form-label">社会信用代码 *</label>
                    <input type="text" name="credit_code" required maxlength="50" class="form-input form-input-lg" placeholder="18位统一社会信用代码，用于企业实名认证">
                </div>
                <div>
                    <label class="form-label">所属行业 *</label>
                    <input type="text" name="industry" required maxlength="50" class="form-input form-input-lg" placeholder="例：互联网/电子商务、教育培训、金融">
                </div>
                <div>
                    <label class="form-label">企业规模</label>
                    <input type="text" name="scale" maxlength="30" class="form-input form-input-lg" placeholder="例：50-100人、500人以上">
                </div>
                <div>
                    <label class="form-label">联系人 *</label>
                    <input type="text" name="contact_name" required maxlength="30" class="form-input form-input-lg" placeholder="企业招聘负责人姓名">
                </div>
                <div>
                    <label class="form-label">联系人电话 *</label>
                    <input type="text" name="contact_phone" required maxlength="11" class="form-input form-input-lg" placeholder="11位手机号，学校审核时可能会拨打核实">
                </div>
                <div>
                    <label class="form-label">密码 *</label>
                    <input type="password" name="password" required minlength="6" maxlength="50" class="form-input form-input-lg" placeholder="6位以上，大小写字母+数字安全性更高">
                </div>
                <div>
                    <label class="form-label">确认密码 *</label>
                    <input type="password" name="password_confirmation" required minlength="6" maxlength="50" class="form-input form-input-lg" placeholder="请再次输入密码">
                </div>
                <div>
                    <label class="form-label">企业简介</label>
                    <textarea name="intro" maxlength="500" class="form-input form-input-lg" rows="2" placeholder="简要介绍企业主营业务、发展历程等（500字以内，选填）"></textarea>
                </div>
                <div>
                    <label class="form-label">营业执照 *</label>
                    <input type="file" name="doc_license" required accept=".png,.jpg,.jpeg,.pdf" class="text-sm" style="width:100%;" title="上传营业执照扫描件或清晰照片，支持 PNG/JPG/PDF">
                </div>
                <div>
                    <label class="form-label">经办人身份证 *</label>
                    <input type="file" name="doc_id_card" required accept=".png,.jpg,.jpeg,.pdf" class="text-sm" style="width:100%;" title="上传招聘负责人身份证正反面，支持 PNG/JPG/PDF">
                </div>
                <div>
                    <label class="form-label">招聘授权书 *</label>
                    <input type="file" name="doc_authorization" required accept=".png,.jpg,.jpeg,.pdf" class="text-sm" style="width:100%;" title="企业授权招聘的授权书，须加盖公章">
                </div>
                <div>
                    <label class="form-label">验证码 *</label>
                    <div class="flex gap-xs"><input type="text" name="captcha" required maxlength="4" class="form-input form-input-lg" placeholder="4位验证码，点击图片刷新" style="flex:1;"><img id="captchaImg2" src="/api/auth/captcha" onclick="this.src='/api/auth/captcha?'+Date.now()" style="height:40px;cursor:pointer;border-radius:6px;" title="点击刷新"></div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary w-full mt-lg" style="padding:12px;font-size:16px;justify-content:center;">注册企业账号</button>
        </form>

        <div class="auth-link">
            <span id="roleSwitchHint">企业用户？<a href="javascript:void(0)" onclick="switchRole('enterprise')">注册企业账号</a></span>
            <span style="margin:0 8px;">|</span>
            已有账号？<a href="/login">去登录</a>
        </div>
    </div>
</div>

<script>
function switchRole(role) {
    document.getElementById('studentForm').style.display = role === 'student' ? '' : 'none';
    document.getElementById('enterpriseForm').style.display = role === 'enterprise' ? '' : 'none';
    document.getElementById('roleSwitchHint').innerHTML = role === 'student'
        ? '企业用户？<a href="javascript:void(0)" onclick="switchRole(\'enterprise\')">注册企业账号</a>'
        : '学生用户？<a href="javascript:void(0)" onclick="switchRole(\'student\')">注册学生账号</a>';
    document.getElementById('flash').style.display = 'none';
    clearErrors();
}

async function doRegister(e, role) {
    e.preventDefault();
    clearErrors();
    const fd = new FormData(e.target);
    const url = '/api/auth/register/' + role;
    try {
        const r = await fetch(url, { method:'POST', body:fd, headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest','X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]').content} });
        const d = await r.json();
        if (d.code === 200) {
            location.href = role === 'enterprise' ? '/enterprise/waiting' : '/';
        } else {
            if (d.errors) showFieldErrors(d.errors);
            else {
                const flash = document.getElementById('flash');
                flash.style.display = 'block';
                flash.className = 'flash flash-error';
                flash.textContent = d.message || '注册失败';
            }
            document.querySelectorAll('img[src*="captcha"]').forEach(img => img.src = '/api/auth/captcha?' + Date.now());
        }
    } catch(err) {
        const flash = document.getElementById('flash');
        flash.style.display = 'block';
        flash.className = 'flash flash-error';
        flash.textContent = '网络错误';
    }
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
            if (field === 'captcha') {
                input.closest('.form-group').appendChild(err);
            } else {
                input.parentNode.appendChild(err);
            }
        } else {
            const flash = document.getElementById('flash');
            flash.style.display = 'block';
            flash.className = 'flash flash-error';
            flash.textContent = msgs.join(', ');
        }
    }
}

function clearErrors() {
    document.querySelectorAll('.field-error').forEach(e => e.remove());
    document.querySelectorAll('input[style*="border-color"], select[style*="border-color"]').forEach(e => e.style.borderColor = '#cbd5e1');
}
</script>
@endsection
