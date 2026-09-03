@extends('frontend.layout')

@section('title', '岗位详情')

@section('content')
<div class="container-md mt-2xl">
    <div id="jobDetail"></div>
</div>

<script>
const jobId = window.location.pathname.split('/').pop();

async function loadJob() {
    try {
        const r = await fetch('/api/jobs/' + jobId, {headers:{'Accept':'application/json'}});
        const d = await r.json();
        if (d.code !== 200) { document.getElementById('jobDetail').innerHTML = '<div class="card empty-state-card text-danger">岗位不存在</div>'; return; }
        const j = d.data;
        const html = `
            <div class="card">
                <a href="/enterprises/${j.enterprise.id}" class="btn-back mb-lg" onclick="return goBack('/enterprises/${j.enterprise.id}')">&larr; 返回</a>
                <h1 class="text-3xl mb-xs">${j.title}</h1>
                <div class="mb-lg">
                    <span class="tag tag-green">${j.enterprise?.name || ''}</span>
                    <span class="tag tag-blue ml-sm">${j.enterprise.industry || '-'}</span>
                    <span class="tag tag-gray ml-sm">${j.city}</span>
                    <span class="tag tag-yellow ml-sm">${j.type === 'full-time' ? '全职' : '实习'}</span>
                </div>
                <div class="text-4xl font-bold text-danger mb-xs">${j.salary_min / 1000}k - ${j.salary_max / 1000}k<span class="text-sm text-light" style="font-weight:400;"> /月</span></div>
            </div>

            <div class="card">
                <h3 class="section-title">岗位信息</h3>
                <table class="info-table">
                    <tr><td class="info-label">招聘人数</td><td>${j.count} 人</td></tr>
                    <tr><td class="info-label">学历要求</td><td>${j.education}</td></tr>
                    <tr><td class="info-label">专业要求</td><td>${j.major || '不限'}</td></tr>
                    <tr><td class="info-label">技能要求</td><td>${j.skills || '-'}</td></tr>
                </table>
            </div>

            <div class="card">
                <h3 class="section-title">岗位职责</h3>
                <div class="text-base" style="line-height:1.8;white-space:pre-wrap;">${j.duty}</div>
            </div>

            <div class="card">
                <h3 class="section-title">任职要求</h3>
                <div class="text-base" style="line-height:1.8;white-space:pre-wrap;">${j.requirement}</div>
            </div>

            ${j.welfare ? `<div class="card"><h3 class="section-title">福利待遇</h3><div class="text-base" style="line-height:1.8;white-space:pre-wrap;">${j.welfare}</div></div>` : ''}

            <div class="card text-center" style="padding:20px;">
                ${document.querySelector('meta[name=user-authenticated]')?.content === '1' ? `<button id="applyBtn" onclick="applyJob()" class="btn btn-primary" style="padding:12px 40px;font-size:16px;">立即投递</button>` : `<a href="/login" class="btn btn-primary" style="padding:12px 40px;font-size:16px;">登录后投递</a>`}
                <div id="applyMsg" class="text-base mt-sm"></div>
            </div>
        `;
        document.getElementById('jobDetail').innerHTML = html;
    } catch(e) {
        document.getElementById('jobDetail').innerHTML = '<div class="card empty-state-card text-danger">加载失败</div>';
    }
}

async function applyJob() {
    const btn = document.getElementById('applyBtn');
    btn.disabled = true;
    btn.textContent = '投递中...';
    try {
        const r = await fetch('/api/jobs/' + jobId + '/apply', {
            method:'POST',
            headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest','X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]').content}
        });
        if (r.status === 401) { location.href = '/login'; return; }
        const d = await r.json();
        const msg = document.getElementById('applyMsg');
        if (d.code === 200) {
            msg.innerHTML = '<div style="background:#ecfdf5;border:1px solid #6ee7b7;border-radius:8px;padding:16px 20px;text-align:center;"><div style="font-size:18px;font-weight:600;color:#065f46;margin-bottom:8px;">🎉 投递成功！</div><div style="font-size:14px;color:#047857;">你的简历已成功投递<br><a href="/student/applications" style="color:#2563eb;font-weight:500;">查看我的投递 →</a></div></div>';
            btn.style.display = 'none';
        } else if (d.code === 422 && d.message.indexOf('已投递') !== -1) {
            showToast(d.message || '已投递过该岗位');
            btn.disabled = false;
            btn.textContent = '立即投递';
        } else {
            msg.innerHTML = '<span class="text-danger">' + (d.message || '投递失败') + '</span>';
            btn.disabled = false;
            btn.textContent = '立即投递';
        }
    } catch(e) {
        document.getElementById('applyMsg').innerHTML = '<span class="text-danger">网络错误</span>';
        btn.disabled = false;
        btn.textContent = '立即投递';
    }
}

loadJob();
</script>
@endsection
