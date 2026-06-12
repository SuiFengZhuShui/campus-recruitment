@extends('frontend.layout')

@section('title', '岗位详情')

@section('content')
<div class="container" style="max-width:800px;margin-top:24px;">
    <div id="jobDetail"></div>
</div>

<script>
const jobId = window.location.pathname.split('/').pop();

async function loadJob() {
    try {
        const r = await fetch('/api/jobs/' + jobId);
        const d = await r.json();
        if (d.code !== 200) { document.getElementById('jobDetail').innerHTML = '<div class="card" style="text-align:center;padding:60px;color:#ef4444;">岗位不存在</div>'; return; }
        const j = d.data;
        const html = `
            <div class="card">
                <a href="/" style="display:inline-flex;align-items:center;gap:4px;padding:8px 18px;background:linear-gradient(135deg,#c7915c,#d4a574);color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:500;text-decoration:none;margin-bottom:16px;">&larr; 返回岗位列表</a>
                <h1 style="font-size:22px;margin-bottom:4px;">${j.title}</h1>
                <div style="margin-bottom:16px;">
                    <span class="tag tag-green">${j.enterprise?.name || ''}</span>
                    <span class="tag tag-blue" style="margin-left:8px;">${j.industry || '-'}</span>
                    <span class="tag tag-gray" style="margin-left:8px;">${j.city}</span>
                    <span class="tag tag-yellow" style="margin-left:8px;">${j.type === 'full-time' ? '全职' : '实习'}</span>
                </div>
                <div style="font-size:28px;font-weight:700;color:#ef4444;margin-bottom:4px;">${j.salary_min / 1000}k - ${j.salary_max / 1000}k</div>
                <div style="font-size:13px;color:#94a3b8;">/ 月</div>
            </div>

            <div class="card">
                <h3 style="font-size:16px;margin-bottom:12px;">岗位信息</h3>
                <table style="width:100%;font-size:14px;">
                    <tr><td style="padding:6px 0;color:#64748b;width:80px;">招聘人数</td><td>${j.count} 人</td></tr>
                    <tr><td style="padding:6px 0;color:#64748b;">学历要求</td><td>${j.education}</td></tr>
                    <tr><td style="padding:6px 0;color:#64748b;">专业要求</td><td>${j.major || '不限'}</td></tr>
                    <tr><td style="padding:6px 0;color:#64748b;">技能要求</td><td>${j.skills || '-'}</td></tr>
                </table>
            </div>

            <div class="card">
                <h3 style="font-size:16px;margin-bottom:12px;">岗位职责</h3>
                <div style="font-size:14px;line-height:1.8;white-space:pre-wrap;">${j.duty}</div>
            </div>

            <div class="card">
                <h3 style="font-size:16px;margin-bottom:12px;">任职要求</h3>
                <div style="font-size:14px;line-height:1.8;white-space:pre-wrap;">${j.requirement}</div>
            </div>

            ${j.welfare ? `<div class="card"><h3 style="font-size:16px;margin-bottom:12px;">福利待遇</h3><div style="font-size:14px;line-height:1.8;white-space:pre-wrap;">${j.welfare}</div></div>` : ''}

            <div class="card" style="text-align:center;padding:20px;">
                ${document.querySelector('meta[name=csrf-token]') ? `<button id="applyBtn" onclick="applyJob()" class="btn btn-primary" style="padding:12px 40px;font-size:16px;">立即投递</button>` : `<a href="/login" class="btn btn-primary" style="padding:12px 40px;font-size:16px;">登录后投递</a>`}
                <div id="applyMsg" style="margin-top:8px;font-size:14px;"></div>
            </div>
        `;
        document.getElementById('jobDetail').innerHTML = html;
    } catch(e) {
        document.getElementById('jobDetail').innerHTML = '<div class="card" style="text-align:center;padding:60px;color:#ef4444;">加载失败</div>';
    }
}

async function applyJob() {
    const btn = document.getElementById('applyBtn');
    btn.disabled = true;
    btn.textContent = '投递中...';
    try {
        const r = await fetch('/api/jobs/' + jobId + '/apply', {
            method:'POST',
            headers:{'X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]').content,'Accept':'application/json'}
        });
        const d = await r.json();
        const msg = document.getElementById('applyMsg');
        if (d.code === 200) {
            msg.innerHTML = '<span style="color:#16a34a;">投递成功！<a href="/student/applications">查看我的投递</a></span>';
            btn.style.display = 'none';
        } else {
            msg.innerHTML = '<span style="color:#ef4444;">' + (d.message || '投递失败') + '</span>';
            btn.disabled = false;
            btn.textContent = '立即投递';
        }
    } catch(e) {
        document.getElementById('applyMsg').innerHTML = '<span style="color:#ef4444;">网络错误</span>';
        btn.disabled = false;
        btn.textContent = '立即投递';
    }
}

loadJob();
</script>
@endsection
