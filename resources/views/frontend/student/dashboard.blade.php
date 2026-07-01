@extends('frontend.layout')

@section('title', '个人中心')

@section('content')
<div class="container" style="margin-top:24px;">
    <a href="/" class="btn-back mb-lg" onclick="return goBack('/')">&larr; 返回</a>
    <h1 class="text-3xl mb-lg" style="font-weight:700;">个人中心</h1>

    <div class="dash-stats">
        <a href="/student/applications" class="stat-card card-hover">
            <div class="stat-label">投递记录</div>
            <div class="stat-value" id="applyCount">--</div>
        </a>
        <a href="/student/interviews" class="stat-card card-hover">
            <div class="stat-label">面试邀请</div>
            <div class="stat-value text-primary" id="interviewCount">--</div>
        </a>
        <a href="/student/offers" class="stat-card card-hover">
            <div class="stat-label">录用通知</div>
            <div class="stat-value text-success-dark" id="offerCount">--</div>
        </a>
        <a href="/student/resume" class="stat-card card-hover">
            <div class="stat-label">我的简历</div>
            <div id="resumeStatus" style="font-size:16px;margin-top:8px;">--</div>
        </a>
    </div>

    <div id="flash" class="hidden"></div>

    <div class="dash-bottom">
        <div class="card">
            <h3 class="section-title">编辑资料</h3>
            <form onsubmit="updateProfile(event)" class="profile-form">
                <div class="form-group">
                    <label class="form-label">姓名</label>
                    <input type="text" name="name" id="pName" maxlength="50" class="form-input form-input-lg">
                </div>
                <div class="form-group">
                    <label class="form-label">邮箱</label>
                    <input type="email" name="email" id="pEmail" maxlength="100" class="form-input form-input-lg">
                </div>
                <div class="form-group">
                    <label class="form-label">班级</label>
                    <input type="text" name="class_name" id="pClass" maxlength="100" class="form-input form-input-lg">
                </div>
                <div class="form-group">
                    <label class="form-label">学号</label>
                    <input type="text" id="pStudentNo" disabled class="form-input form-input-lg">
                </div>
                <div class="form-group">
                    <label class="form-label">手机号</label>
                    <input type="text" id="pPhone" disabled class="form-input form-input-lg">
                </div>
                <div class="form-group">
                    <label class="form-label">学院</label>
                    <input type="text" id="pCollege" disabled class="form-input form-input-lg">
                </div>
                <button type="submit" class="btn btn-primary mt-md">保存资料</button>
            </form>
        </div>

        <div class="dash-quick-links" style="gap:var(--space-lg);">
            <div class="card">
                <h3 class="section-title">简历</h3>
                <div id="resumeSection"></div>
            </div>
            <div class="card">
                <h3 class="section-title">快捷入口</h3>
                <div class="dash-quick-links">
                    <a href="/jobs">→ 浏览岗位</a>
                    <a href="/student/applications">→ 我的投递</a>
                    <a href="/student/resume">→ 上传简历</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
async function loadDashboard() {
    try {
        const [profileR, appR, intR, offerR] = await Promise.all([
            fetch('/api/my/profile', {headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest'}}),
            fetch('/api/my/applications?per_page=1', {headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest'}}),
            fetch('/api/my/interviews', {headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest'}}),
            fetch('/api/my/offers', {headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest'}})
        ]);

        if (profileR.status === 401) { location.href = '/login'; return; }

        const [profile, app, intD, offerD] = await Promise.all([
            profileR.json(), appR.json(), intR.json(), offerR.json()
        ]);

        // Stats
        if (app.code === 200) {
            document.getElementById('applyCount').textContent = app.data?.meta?.total || 0;
        }
        if (intD.code === 200) {
            document.getElementById('interviewCount').textContent = intD.data?.length || 0;
        }
        if (offerD.code === 200) {
            document.getElementById('offerCount').textContent = offerD.data?.length || 0;
        }

        // Profile form + resume
        if (profile.code === 200 && profile.data) {
            const s = profile.data;
            document.getElementById('resumeStatus').textContent = s.resume_path ? '已上传' : '未上传';
            document.getElementById('pName').value = s.user?.name || '';
            document.getElementById('pEmail').value = s.user?.email || '';
            document.getElementById('pClass').value = s.class_name || '';
            document.getElementById('pStudentNo').value = s.student_no || '';
            document.getElementById('pPhone').value = s.user?.phone || '';
            document.getElementById('pCollege').value = s.college?.name || '未匹配';

            document.getElementById('resumeSection').innerHTML = s.resume_path ? `
                <div class="text-base text-sub mb-sm">已上传简历</div>
                <a href="/api/my/resume" class="btn btn-outline mr-sm">下载简历</a>
                <form onsubmit="uploadResume(event)" enctype="multipart/form-data" class="mt-md">
                    <label class="form-label">更新简历（PDF/Word ≤10MB）</label>
                    <div class="flex gap-sm">
                        <input type="file" name="resume" accept=".pdf,.doc,.docx" class="text-sm" style="flex:1;">
                        <button type="submit" class="btn btn-primary btn-sm">上传</button>
                    </div>
                </form>
            ` : `
                <div class="text-base text-light mb-sm">未上传简历</div>
                <form onsubmit="uploadResume(event)" enctype="multipart/form-data">
                    <label class="form-label">上传简历（PDF/Word ≤10MB）</label>
                    <div class="flex gap-sm">
                        <input type="file" name="resume" accept=".pdf,.doc,.docx" class="text-sm" style="flex:1;">
                        <button type="submit" class="btn btn-primary btn-sm">上传</button>
                    </div>
                </form>
            `;
        }
    } catch(e) {}
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
        showFlash(d.code === 200 ? '保存成功' : (d.message || '保存失败'), d.code === 200);
    } catch(err) { showFlash('网络错误', false); }
}

async function uploadResume(e) {
    e.preventDefault();
    const fd = new FormData(e.target);
    try {
        const r = await fetch('/api/my/resume', { method:'POST', body:fd, headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest','X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]').content} });
        const d = await r.json();
        showFlash(d.code === 200 ? '简历上传成功' : (d.message || '上传失败'), d.code === 200);
        if (d.code === 200) loadDashboard();
    } catch(err) { showFlash('网络错误', false); }
}

function showFlash(msg, ok) {
    const el = document.getElementById('flash');
    el.style.display = 'block';
    el.className = 'flash flash-' + (ok ? 'success' : 'error');
    el.textContent = msg;
}

loadDashboard();
</script>
@endsection
