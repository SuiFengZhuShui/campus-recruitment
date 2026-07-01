@extends('frontend.layout')

@section('title', '我的投递')

@section('content')
<div class="container mt-2xl">
    <a href="/student/dashboard" class="btn-back mb-lg" onclick="return goBack('/student/dashboard')">&larr; 返回个人中心</a>
    <h1 class="page-title">我的投递</h1>
    <div id="appList"></div>
</div>

<script>
const STATUS_MAP = {pending:'待审核',reviewed:'已查看',interviewed:'面试中',accepted:'已录用',rejected:'未通过'};

async function load() {
    try {
        const r = await fetch('/api/my/applications', {headers:{'Accept':'application/json'}});
        const d = await r.json();
        const list = (d.data && d.data.list) ? d.data.list : [];
        if (!list.length) {
            document.getElementById('appList').innerHTML = '<div class="card empty-state-card">还没有投递任何岗位，<a href="/" class="text-primary">去看看</a></div>';
            return;
        }
        document.getElementById('appList').innerHTML = list.map(a => {
            const statusColors = {pending:'#92400e',reviewed:'#1e40af',interviewed:'#7c3aed',accepted:'#065f46',rejected:'#991b1b'};
            const statusBgs = {pending:'#fef3c7',reviewed:'#dbeafe',interviewed:'#ede9fe',accepted:'#d1fae5',rejected:'#fee2e2'};
            return `
            <div class="card" style="cursor:pointer;" onclick="toggleDetail(this, ${a.id})">
                <div class="flex-between">
                    <div style="flex:1;">
                        <div class="app-job-title">${a.job?.title || '-'}</div>
                        <div class="app-job-meta">
                            <span class="tag tag-green">${a.job?.enterprise?.name || ''}</span>
                            <span class="tag tag-gray ml-sm">${a.job?.city || ''}</span>
                            <span class="tag tag-yellow ml-sm">${a.job?.type === 'full-time' ? '全职' : '实习'}</span>
                        </div>
                    </div>
                    <div class="app-status-area">
                        <span style="display:inline-block;padding:2px 10px;border-radius:12px;font-size:12px;font-weight:600;color:${statusColors[a.status]||'#64748b'};background:${statusBgs[a.status]||'#f1f5f9'};">${STATUS_MAP[a.status]||'未知'}</span>
                        <div class="app-date">${new Date(a.created_at).toLocaleDateString('zh-CN')}</div>
                    </div>
                </div>
                <div class="app-detail" id="detail-${a.id}" style="display:none;margin-top:12px;padding-top:12px;border-top:1px solid #e2e8f0;font-size:13px;color:#64748b;">
                    <div>投递时间：${new Date(a.created_at).toLocaleString('zh-CN')}</div>
                    <div>当前状态：${STATUS_MAP[a.status]||'未知'}</div>
                    ${a.job?.salary_min ? `<div>薪资范围：${a.job.salary_min/1000}k-${a.job.salary_max/1000}k /月</div>` : ''}
                    ${a.job?.education ? `<div>学历要求：${a.job.education}</div>` : ''}
                    ${a.remark ? `<div class="note-box mt-sm">备注：${a.remark}</div>` : ''}
                </div>
            </div>
        `}).join('');
    } catch(e) {
        document.getElementById('appList').innerHTML = '<div class="card empty-state-card text-danger">加载失败，请先<a href="/login" class="text-primary">登录</a></div>';
    }
}

function toggleDetail(card, id) {
    const detail = document.getElementById('detail-' + id);
    if (detail.style.display === 'none') {
        detail.style.display = 'block';
        card.style.boxShadow = '0 2px 12px rgba(0,0,0,.08)';
    } else {
        detail.style.display = 'none';
        card.style.boxShadow = '';
    }
}
load();
</script>
@endsection
