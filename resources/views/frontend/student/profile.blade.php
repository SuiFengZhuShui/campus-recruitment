@extends('frontend.layout')

@section('title', '个人档案')

@section('content')
<div class="container-sm mt-2xl">
    <a href="/" class="btn-back mb-lg" onclick="return goBack('/')">&larr; 返回</a>
    <h1 class="page-title">个人档案</h1>
    <div id="flash" class="hidden"></div>
    <div id="profileContent"></div>
</div>

<script>
async function loadProfile() {
    try {
        const r = await fetch('/api/my/profile', {headers:{'Accept':'application/json'}});
        const d = await r.json();
        if (d.code !== 200) { document.getElementById('profileContent').innerHTML = '<div class="card empty-state-card text-danger">'+(d.message||'加载失败')+'</div>'; return; }
        const s = d.data;
        const html = `
            <form onsubmit="updateProfile(event)" class="profile-form card">
                <h3 class="profile-section-title">基本信息</h3>
                <div class="profile-grid">
                    <div><label class="form-label">姓名</label><input type="text" name="name" id="pName" value="${s.user?.name || ''}" maxlength="50" class="form-input form-input-lg"></div>
                    <div><label class="form-label">邮箱</label><input type="email" name="email" id="pEmail" value="${s.user?.email || ''}" maxlength="100" class="form-input form-input-lg"></div>
                    <div><label class="form-label">学号</label><input type="text" value="${s.student_no || ''}" disabled class="form-input form-input-lg"></div>
                    <div><label class="form-label">班级</label><input type="text" name="class_name" id="pClass" value="${s.class_name || ''}" maxlength="100" class="form-input form-input-lg"></div>
                    <div><label class="form-label">手机号</label><input type="text" value="${s.user?.phone || ''}" disabled class="form-input form-input-lg"></div>
                    <div><label class="form-label">学院</label><input type="text" value="${s.college?.name || '未匹配'}" disabled class="form-input form-input-lg"></div>
                </div>
                <button type="submit" class="btn btn-primary mt-md">保存资料</button>
            </form>

            <div class="card">
                <h3 class="profile-section-title">简历</h3>
                ${s.resume_path ? `
                    <div class="text-base text-sub mb-sm">已上传简历</div>
                    <a href="/api/my/resume" class="btn btn-outline mr-sm">下载简历</a>
                ` : '<div class="text-base text-light mb-sm">未上传简历</div>'}
                <form onsubmit="uploadResume(event)" enctype="multipart/form-data" class="mt-sm">
                    <label class="form-label">上传新简历（PDF/Word ≤10MB）</label>
                    <div class="flex gap-sm">
                        <input type="file" name="resume" accept=".pdf,.doc,.docx" class="text-sm" style="flex:1;">
                        <button type="submit" class="btn btn-primary btn-sm">上传</button>
                    </div>
                </form>
            </div>
        `;
        document.getElementById('profileContent').innerHTML = html;
    } catch(e) {
        document.getElementById('profileContent').innerHTML = '<div class="card empty-state-card text-danger">加载失败，请先<a href="/login" class="text-primary">登录</a></div>';
    }
}

async function updateProfile(e) {
    e.preventDefault();
    const fd = new FormData();
    fd.append('name', document.getElementById('pName').value);
    fd.append('email', document.getElementById('pEmail').value);
    fd.append('class_name', document.getElementById('pClass').value);
    fd.append('_method', 'PUT');
    try {
        const r = await fetch('/api/my/profile', { method:'POST', body:fd, headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest','X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]').content} });
        const d = await r.json();
        showFlash(d.code === 200 ? '保存成功' : (d.message || '保存失败'), d.code === 200 ? 'success' : 'error');
    } catch(err) { showFlash('网络错误', 'error'); }
}

async function uploadResume(e) {
    e.preventDefault();
    const fd = new FormData(e.target);
    try {
        const r = await fetch('/api/my/resume', { method:'POST', body:fd, headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest','X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]').content} });
        const d = await r.json();
        showFlash(d.code === 200 ? '简历上传成功' : (d.message || '上传失败'), d.code === 200 ? 'success' : 'error');
        if (d.code === 200) loadProfile();
    } catch(err) { showFlash('网络错误', 'error'); }
}

function showFlash(msg, type) {
    const el = document.getElementById('flash');
    el.style.display = 'block';
    el.className = 'flash flash-' + (type === 'error' ? 'error' : 'success');
    el.textContent = msg;
}

loadProfile();
</script>
@endsection
