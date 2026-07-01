@extends('frontend.layout')

@section('title', '企业信息')

@section('content')
<div class="container" style="margin-top:24px;max-width:600px;">
    <a href="/enterprise/dashboard" class="btn-back mb-lg" onclick="return goBack('/enterprise/dashboard')">&larr; 返回企业后台</a>

    <div id="flash"></div>

    <!-- Profile -->
    <div class="card" style="padding:24px;margin-bottom:24px;">
        <h2 class="section-title">企业信息</h2>
        <form id="profileForm" onsubmit="saveProfile(event)">
            <div class="form-group">
                <label class="form-label">企业名称</label>
                <input type="text" name="name" id="pName" maxlength="200" class="form-input form-input-lg">
            </div>
            <div class="form-group">
                <label class="form-label">所属行业</label>
                <input type="text" name="industry" id="pIndustry" maxlength="50" class="form-input form-input-lg">
            </div>
            <div class="form-group">
                <label class="form-label">企业规模</label>
                <input type="text" name="scale" id="pScale" maxlength="30" class="form-input form-input-lg" placeholder="例：50-100人">
            </div>
            <div class="form-group">
                <label class="form-label">联系人</label>
                <input type="text" name="contact_name" id="pContactName" maxlength="30" class="form-input form-input-lg">
            </div>
            <div class="form-group">
                <label class="form-label">联系电话</label>
                <input type="text" name="contact_phone" id="pContactPhone" maxlength="11" class="form-input form-input-lg">
            </div>
            <div class="form-group">
                <label class="form-label">邮箱</label>
                <input type="email" name="email" id="pEmail" maxlength="100" class="form-input form-input-lg">
            </div>
            <div class="form-group">
                <label class="form-label">企业简介</label>
                <textarea name="intro" id="pIntro" maxlength="500" rows="3" class="form-input form-input-lg"></textarea>
            </div>
            <button type="submit" class="btn btn-primary w-full mt-md" style="padding:12px;font-size:16px;justify-content:center;">保存信息</button>
        </form>
    </div>

    <!-- Password -->
    <div class="card" style="padding:24px;">
        <h2 class="section-title">修改密码</h2>
        <form id="passwordForm" onsubmit="changePassword(event)">
            <div class="form-group">
                <label class="form-label">当前密码</label>
                <input type="password" name="current_password" id="cpw" required class="form-input form-input-lg">
            </div>
            <div class="form-group">
                <label class="form-label">新密码</label>
                <input type="password" name="password" id="npw" required minlength="6" maxlength="50" class="form-input form-input-lg" placeholder="6位以上">
            </div>
            <div class="form-group">
                <label class="form-label">确认新密码</label>
                <input type="password" name="password_confirmation" id="npw2" required minlength="6" maxlength="50" class="form-input form-input-lg">
            </div>
            <button type="submit" class="btn btn-primary w-full mt-md" style="padding:12px;font-size:16px;justify-content:center;">修改密码</button>
        </form>
    </div>
</div>

<script>
async function loadProfile() {
    try {
        const r = await fetch('/api/my/enterprise', {headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest'}});
        if (r.status === 401) { location.href = '/login'; return; }
        const d = await r.json();
        if (d.code === 403) { location.href = '/enterprise/waiting'; return; }
        if (d.code !== 200) return;
        const e = d.data;
        document.getElementById('pName').value = e.name || '';
        document.getElementById('pIndustry').value = e.industry || '';
        document.getElementById('pScale').value = e.scale || '';
        document.getElementById('pContactName').value = e.contact_name || '';
        document.getElementById('pContactPhone').value = e.contact_phone || '';
        document.getElementById('pEmail').value = e.user?.email || '';
        document.getElementById('pIntro').value = e.intro || '';
    } catch(e) {}
}

async function saveProfile(e) {
    e.preventDefault();
    const data = {
        name: document.getElementById('pName').value,
        industry: document.getElementById('pIndustry').value,
        scale: document.getElementById('pScale').value,
        contact_name: document.getElementById('pContactName').value,
        contact_phone: document.getElementById('pContactPhone').value,
        email: document.getElementById('pEmail').value,
        intro: document.getElementById('pIntro').value,
    };
    try {
        const r = await fetch('/api/my/enterprise', {
            method:'PUT',
            headers:{'Accept':'application/json','Content-Type':'application/json','X-Requested-With':'XMLHttpRequest','X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]').content},
            body: JSON.stringify(data)
        });
        const d = await r.json();
        const flash = document.getElementById('flash');
        if (d.code === 200) {
            flash.innerHTML = '<div class="flash flash-success">✅ 信息已更新</div>';
        } else {
            flash.innerHTML = '<div class="flash flash-error">❌ ' + (d.message || '更新失败') + '</div>';
        }
    } catch(e) {
        document.getElementById('flash').innerHTML = '<div class="flash flash-error">网络错误</div>';
    }
}

async function changePassword(e) {
    e.preventDefault();
    const data = {
        current_password: document.getElementById('cpw').value,
        password: document.getElementById('npw').value,
        password_confirmation: document.getElementById('npw2').value,
    };
    try {
        const r = await fetch('/api/my/password', {
            method:'PUT',
            headers:{'Accept':'application/json','Content-Type':'application/json','X-Requested-With':'XMLHttpRequest','X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]').content},
            body: JSON.stringify(data)
        });
        const d = await r.json();
        const flash = document.getElementById('flash');
        if (d.code === 200) {
            flash.innerHTML = '<div class="flash flash-success">✅ 密码已修改</div>';
            document.getElementById('cpw').value = '';
            document.getElementById('npw').value = '';
            document.getElementById('npw2').value = '';
        } else {
            flash.innerHTML = '<div class="flash flash-error">❌ ' + (d.message || '修改失败') + '</div>';
        }
    } catch(e) {
        document.getElementById('flash').innerHTML = '<div class="flash flash-error">网络错误</div>';
    }
}

loadProfile();
</script>
@endsection
