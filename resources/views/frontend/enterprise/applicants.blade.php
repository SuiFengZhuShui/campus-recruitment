@extends('frontend.layout')

@section('title', '投递列表')

@section('content')
<div class="container" style="margin-top:24px;">
    <a href="/enterprise/jobs" style="display:inline-flex;align-items:center;gap:4px;padding:8px 18px;background:linear-gradient(135deg,#c7915c,#d4a574);color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:500;text-decoration:none;margin-bottom:16px;">&larr; 返回岗位管理</a>
    <h1 style="font-size:20px;margin-bottom:20px;" id="jobTitle">投递列表</h1>
    <div id="appList"></div>
</div>

<script>
const jobId = window.location.pathname.split('/')[2]; // /enterprise/jobs/{id}/applications

async function load() {
    try {
        const r = await fetch('/api/my/jobs/' + jobId + '/applications', {headers:{'Accept':'application/json'}});
        const d = await r.json();
        if (d.code !== 200) { document.getElementById('appList').innerHTML = '<div class="card" style="text-align:center;padding:60px;color:#ef4444;">加载失败</div>'; return; }
        document.getElementById('jobTitle').textContent = d.data.job?.title + ' — 投递列表';
        const list = d.data.list || [];
        if (!list.length) {
            document.getElementById('appList').innerHTML = '<div class="card" style="text-align:center;padding:60px;color:#94a3b8;">暂无投递</div>';
            return;
        }
        document.getElementById('appList').innerHTML = list.map(a => `
            <div class="card">
                <div style="display:flex;justify-content:space-between;">
                    <div>
                        <strong>${a.student?.user?.name || '-'}</strong>
                        <span class="tag tag-blue" style="margin-left:8px;">${a.student?.student_no || ''}</span>
                        <span class="tag tag-gray" style="margin-left:8px;">${a.student?.class_name || ''}</span>
                    </div>
                    <div style="font-size:13px;color:#94a3b8;">${new Date(a.created_at).toLocaleDateString('zh-CN')}</div>
                </div>
                <div style="margin-top:8px;font-size:14px;color:#64748b;">
                    手机：${a.student?.user?.phone || '-'} | 邮箱：${a.student?.user?.email || '-'}
                    ${a.student?.resume_path ? `<a href="/api/resume/${a.student?.id}" style="color:#3b82f6;margin-left:12px;">下载简历</a>` : '<span style="color:#94a3b8;margin-left:8px;">未上传简历</span>'}
                </div>
            </div>
        `).join('');
    } catch(e) {
        document.getElementById('appList').innerHTML = '<div class="card" style="text-align:center;padding:60px;color:#ef4444;">加载失败</div>';
    }
}
load();
</script>
@endsection
