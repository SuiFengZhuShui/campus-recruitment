@extends('frontend.layout')

@section('title', '投递列表')

@section('content')
<div class="container" style="margin-top:24px;">
    <a href="/enterprise/jobs" style="display:inline-flex;align-items:center;gap:4px;padding:8px 18px;background:linear-gradient(135deg,#c7915c,#d4a574);color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:500;text-decoration:none;margin-bottom:16px;">&larr; 返回岗位管理</a>
    <h1 style="font-size:20px;margin-bottom:20px;" id="jobTitle">投递列表</h1>
    <div id="appList"></div>
</div>

<script>
const jobId = window.location.pathname.split('/')[3]; // /enterprise/jobs/{id}/applications
const STATUS_MAP = {pending:'待审核',reviewed:'已查看',interviewed:'面试中',accepted:'已录用',rejected:'未通过'};
const STATUS_COLOR = {pending:'#92400e',reviewed:'#1e40af',interviewed:'#7c3aed',accepted:'#065f46',rejected:'#991b1b'};
const STATUS_BG = {pending:'#fef3c7',reviewed:'#dbeafe',interviewed:'#ede9fe',accepted:'#d1fae5',rejected:'#fee2e2'};

async function load() {
    try {
        const r = await fetch('/api/my/jobs/' + jobId + '/applications', {headers:{'Accept':'application/json'}});
        const d = await r.json();
        if (d.code !== 200) { document.getElementById('appList').innerHTML = '<div class="card" style="text-align:center;padding:60px;color:#ef4444;">加载失败</div>'; return; }
        document.getElementById('jobTitle').textContent = (d.data.job?.title || '岗位') + ' — 投递列表';
        const list = d.data.list || [];
        if (!list.length) {
            document.getElementById('appList').innerHTML = '<div class="card" style="text-align:center;padding:60px;color:#94a3b8;">暂无投递</div>';
            return;
        }
        document.getElementById('appList').innerHTML = list.map(a => `
            <div class="card">
                <div style="display:flex;justify-content:space-between;align-items:flex-start;">
                    <div style="flex:1;">
                        <strong>${a.student?.user?.name || '-'}</strong>
                        <span class="tag tag-blue" style="margin-left:8px;">${a.student?.student_no || ''}</span>
                        <span class="tag tag-gray" style="margin-left:8px;">${a.student?.class_name || ''}</span>
                        <span style="display:inline-block;margin-left:8px;padding:2px 10px;border-radius:12px;font-size:12px;font-weight:600;color:${STATUS_COLOR[a.status]||'#64748b'};background:${STATUS_BG[a.status]||'#f1f5f9'};">${STATUS_MAP[a.status]||'未知'}</span>
                        <div style="margin-top:8px;font-size:14px;color:#64748b;">
                            手机：${a.student?.user?.phone || '-'} | 邮箱：${a.student?.user?.email || '-'}
                            ${a.student?.resume_path ? `<a href="/api/resume/${a.student?.id}" style="color:#3b82f6;margin-left:12px;">下载简历</a>` : '<span style="color:#94a3b8;margin-left:8px;">未上传简历</span>'}
                        </div>
                        ${a.remark ? `<div style="margin-top:6px;font-size:13px;color:#64748b;background:#f8fafc;padding:4px 10px;border-radius:6px;">备注：${a.remark}</div>` : ''}
                    </div>
                    <div style="text-align:right;margin-left:16px;">
                        <div style="font-size:13px;color:#94a3b8;margin-bottom:6px;">${new Date(a.created_at).toLocaleDateString('zh-CN')}</div>
                        <select onchange="updateStatus(${a.id}, this.value)" style="padding:4px 8px;border:1px solid #cbd5e1;border-radius:6px;font-size:13px;">
                            <option value="">- 修改状态 -</option>
                            <option value="reviewed" ${a.status==='reviewed'?'selected':''}>已查看</option>
                            <option value="interviewed" ${a.status==='interviewed'?'selected':''}>面试中</option>
                            <option value="accepted" ${a.status==='accepted'?'selected':''}>已录用</option>
                            <option value="rejected" ${a.status==='rejected'?'selected':''}>未通过</option>
                        </select>
                    </div>
                </div>
            </div>
        `).join('');
    } catch(e) {
        document.getElementById('appList').innerHTML = '<div class="card" style="text-align:center;padding:60px;color:#ef4444;">加载失败</div>';
    }
}

async function updateStatus(applicationId, status) {
    if (!status) return;
    if (!confirm('确认将状态改为「' + STATUS_MAP[status] + '」？')) {
        load(); // Reset select
        return;
    }
    try {
        const r = await fetch('/api/my/jobs/' + jobId + '/applications/' + applicationId + '/status', {
            method: 'PUT',
            headers: {'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]').content},
            body: JSON.stringify({status: status})
        });
        const d = await r.json();
        if (d.code === 200) {
            load(); // Refresh list
        } else {
            alert(d.message || '操作失败');
            load();
        }
    } catch(e) {
        alert('网络错误');
        load();
    }
}

load();
</script>
@endsection
