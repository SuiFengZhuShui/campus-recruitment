@extends('frontend.layout')

@section('title', '我的面试')

@section('content')
<div class="container mt-2xl">
    <a href="/student/dashboard" class="btn-back mb-lg" onclick="return goBack('/student/dashboard')">&larr; 返回个人中心</a>
    <h1 class="page-title">我的面试</h1>
    <div id="interviewList"></div>
</div>

<script>
const STATUS_MAP = {invited:'待确认',accepted:'已接受',declined:'已拒绝',completed:'已完成'};
const TYPE_MAP = {online:'线上','on-site':'线下'};

async function load() {
    try {
        const r = await fetch('/api/my/interviews', {headers:{'Accept':'application/json'}});
        const d = await r.json();
        const list = (d.data && d.data.list) ? d.data.list : [];
        if (!list.length) {
            document.getElementById('interviewList').innerHTML = '<div class="card empty-state-card">暂无面试安排</div>';
            return;
        }
        document.getElementById('interviewList').innerHTML = list.map(iv => {
            const statusColors = {invited:'#92400e',accepted:'#065f46',declined:'#991b1b',completed:'#1e40af'};
            const statusBgs = {invited:'#fef3c7',accepted:'#d1fae5',declined:'#fee2e2',completed:'#dbeafe'};
            return `
            <div class="card">
                <div class="interview-header">
                    <div class="interview-info">
                        <div class="interview-job-title">${iv.application?.job?.title || '-'}</div>
                        <div class="interview-company">${iv.application?.job?.enterprise?.name || ''}</div>
                        <div class="interview-detail">
                            <span class="interview-detail-label">面试时间：</span>${new Date(iv.scheduled_at).toLocaleString('zh-CN')}
                        </div>
                        <div class="interview-detail">
                            <span class="interview-detail-label">地点：</span>${iv.location} <span class="tag tag-blue ml-xs">${TYPE_MAP[iv.type]||iv.type}</span>
                        </div>
                        ${iv.contact ? `<div class="interview-detail"><span class="interview-detail-label">联系人：</span>${iv.contact}</div>` : ''}
                        ${iv.note ? `<div class="note-box mt-sm">备注：${iv.note}</div>` : ''}
                    </div>
                    <div class="interview-actions">
                        <span style="display:inline-block;padding:2px 10px;border-radius:12px;font-size:12px;font-weight:600;color:${statusColors[iv.status]||'#64748b'};background:${statusBgs[iv.status]||'#f1f5f9'};margin-bottom:8px;">${STATUS_MAP[iv.status]||'未知'}</span>
                        ${iv.status === 'invited' ? `
                            <div class="action-group">
                                <button onclick="respond(${iv.id},'accept')" class="btn-accept-xs">接受</button>
                                <button onclick="respond(${iv.id},'decline')" class="btn-decline-xs">拒绝</button>
                            </div>
                        ` : ''}
                    </div>
                </div>
            </div>
        `}).join('');
    } catch(e) {
        document.getElementById('interviewList').innerHTML = '<div class="card empty-state-card text-danger">加载失败，请先<a href="/login" class="text-primary">登录</a></div>';
    }
}

async function respond(id, action) {
    const label = action === 'accept' ? '接受' : '拒绝';
    if (!confirm('确认' + label + '该面试邀请？')) return;
    try {
        const r = await fetch('/api/my/interviews/' + id + '/respond', {
            method:'PUT',
            headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]').content},
            body: JSON.stringify({action: action})
        });
        const d = await r.json();
        if (d.code === 200) load(); else alert(d.message);
    } catch(e) { alert('网络错误'); }
}

load();
</script>
@endsection
