@extends('frontend.layout')

@section('title', '我的录用')

@section('content')
<div class="container mt-2xl">
    <a href="/student/dashboard" class="btn-back mb-lg" onclick="return goBack('/student/dashboard')">&larr; 返回个人中心</a>
    <h1 class="page-title">我的录用通知</h1>
    <div id="offerList"></div>
</div>

<script>
const STATUS_MAP = {draft:'草稿',sent:'待确认',accepted:'已接受',declined:'已拒绝'};

async function load() {
    try {
        const r = await fetch('/api/my/offers', {headers:{'Accept':'application/json'}});
        const d = await r.json();
        const list = (d.data && d.data.list) ? d.data.list : [];
        if (!list.length) {
            document.getElementById('offerList').innerHTML = '<div class="card empty-state-card">暂无录用通知</div>';
            return;
        }
        document.getElementById('offerList').innerHTML = list.map(o => {
            const statusColors = {draft:'#64748b',sent:'#92400e',accepted:'#065f46',declined:'#991b1b'};
            const statusBgs = {draft:'#f1f5f9',sent:'#fef3c7',accepted:'#d1fae5',declined:'#fee2e2'};
            return `
            <div class="card">
                <div class="flex-between-start">
                    <div style="flex:1;">
                        <div class="offer-position">${o.position}</div>
                        <div class="offer-meta">${o.application?.job?.enterprise?.name || ''} | ${o.application?.job?.title || ''}</div>
                        <div class="offer-details">
                            <div class="offer-detail-item">
                                <div class="offer-detail-label">薪资</div>
                                <div class="offer-detail-value">${o.salary}</div>
                            </div>
                            <div class="offer-detail-item">
                                <div class="offer-detail-label">入职日期</div>
                                <div class="offer-detail-value-dark">${o.start_date}</div>
                            </div>
                        </div>
                        ${o.note ? `<div class="note-box mt-sm">备注：${o.note}</div>` : ''}
                    </div>
                    <div class="text-right ml-lg">
                        <span style="display:inline-block;padding:2px 10px;border-radius:12px;font-size:12px;font-weight:600;color:${statusColors[o.status]||'#64748b'};background:${statusBgs[o.status]||'#f1f5f9'};margin-bottom:8px;">${STATUS_MAP[o.status]||'未知'}</span>
                        ${o.status === 'sent' ? `
                            <div class="action-group">
                                <button onclick="respond(${o.id},'accept')" class="btn-accept">接受 Offer</button>
                                <button onclick="respond(${o.id},'decline')" class="btn-decline">拒绝</button>
                            </div>
                        ` : ''}
                    </div>
                </div>
            </div>
        `}).join('');
    } catch(e) {
        document.getElementById('offerList').innerHTML = '<div class="card empty-state-card text-danger">加载失败，请先<a href="/login" class="text-primary">登录</a></div>';
    }
}

async function respond(id, action) {
    const label = action === 'accept' ? '接受' : '拒绝';
    if (!confirm('确认' + label + '该录用通知？')) return;
    try {
        const r = await fetch('/api/my/offers/' + id + '/respond', {
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
