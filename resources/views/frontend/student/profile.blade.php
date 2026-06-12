@extends('frontend.layout')

@section('title', '个人档案')

@section('content')
<div class="container" style="max-width:600px;margin-top:24px;">
    <h1 style="font-size:20px;margin-bottom:20px;">个人档案</h1>
    <div id="flash" style="display:none;"></div>
    <div id="profileContent"></div>
</div>

<script>
async function loadProfile() {
    try {
        const r = await fetch('/api/my/profile', {headers:{'Accept':'application/json'}});
        const d = await r.json();
        if (d.code !== 200) { document.getElementById('profileContent').innerHTML = '<div class="card" style="text-align:center;padding:60px;color:#ef4444;">'+(d.message||'加载失败')+'</div>'; return; }
        const s = d.data;
        const html = `
            <form onsubmit="updateProfile(event)" class="card" style="margin-bottom:16px;">
                <h3 style="font-size:15px;margin-bottom:12px;">基本信息</h3>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                    <div><label style="font-size:13px;color:#475569;">姓名</label><input type="text" name="name" id="pName" value="${s.user?.name || ''}" maxlength="50" style="width:100%;padding:9px 12px;border:1px solid #cbd5e1;border-radius:6px;font-size:14px;"></div>
                    <div><label style="font-size:13px;color:#475569;">邮箱</label><input type="email" name="email" id="pEmail" value="${s.user?.email || ''}" maxlength="100" style="width:100%;padding:9px 12px;border:1px solid #cbd5e1;border-radius:6px;font-size:14px;"></div>
                    <div><label style="font-size:13px;color:#475569;">学号</label><input type="text" value="${s.student_no || ''}" disabled style="width:100%;padding:9px 12px;border:1px solid #e2e8f0;border-radius:6px;font-size:14px;background:#f8fafc;"></div>
                    <div><label style="font-size:13px;color:#475569;">班级</label><input type="text" name="class_name" id="pClass" value="${s.class_name || ''}" maxlength="100" style="width:100%;padding:9px 12px;border:1px solid #cbd5e1;border-radius:6px;font-size:14px;"></div>
                    <div><label style="font-size:13px;color:#475569;">手机号</label><input type="text" value="${s.user?.phone || ''}" disabled style="width:100%;padding:9px 12px;border:1px solid #e2e8f0;border-radius:6px;font-size:14px;background:#f8fafc;"></div>
                    <div><label style="font-size:13px;color:#475569;">学院</label><input type="text" value="${s.college?.name || '未匹配'}" disabled style="width:100%;padding:9px 12px;border:1px solid #e2e8f0;border-radius:6px;font-size:14px;background:#f8fafc;"></div>
                </div>
                <button type="submit" class="btn btn-primary" style="margin-top:12px;">保存资料</button>
            </form>

            <div class="card">
                <h3 style="font-size:15px;margin-bottom:12px;">简历</h3>
                ${s.resume_path ? `
                    <div style="font-size:14px;color:#475569;margin-bottom:8px;">已上传简历</div>
                    <a href="/api/my/resume" class="btn btn-outline" style="margin-right:8px;">下载简历</a>
                ` : '<div style="font-size:14px;color:#94a3b8;margin-bottom:8px;">未上传简历</div>'}
                <form onsubmit="uploadResume(event)" enctype="multipart/form-data" style="margin-top:8px;">
                    <label style="display:block;font-size:13px;color:#475569;margin-bottom:4px;">上传新简历（PDF/Word ≤10MB）</label>
                    <div style="display:flex;gap:8px;">
                        <input type="file" name="resume" accept=".pdf,.doc,.docx" style="flex:1;font-size:13px;">
                        <button type="submit" class="btn btn-primary" style="font-size:13px;">上传</button>
                    </div>
                </form>
            </div>
        `;
        document.getElementById('profileContent').innerHTML = html;
    } catch(e) {
        document.getElementById('profileContent').innerHTML = '<div class="card" style="text-align:center;padding:60px;color:#ef4444;">加载失败，请先<a href="/login">登录</a></div>';
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
        const r = await fetch('/api/my/profile', { method:'POST', body:fd, headers:{'X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]').content} });
        const d = await r.json();
        showFlash(d.code === 200 ? '保存成功' : (d.message || '保存失败'), d.code === 200 ? 'success' : 'error');
    } catch(err) { showFlash('网络错误', 'error'); }
}

async function uploadResume(e) {
    e.preventDefault();
    const fd = new FormData(e.target);
    try {
        const r = await fetch('/api/my/resume', { method:'POST', body:fd, headers:{'X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]').content} });
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
