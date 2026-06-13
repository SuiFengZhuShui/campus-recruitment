@extends('frontend.layout')

@section('title', '我的录用')

@section('content')
<div class="container" style="margin-top:24px;">
    <h1 style="font-size:20px;margin-bottom:20px;">我的录用通知</h1>
    <div id="offerList"></div>
</div>

<script>
const STATUS_MAP = {draft:'草稿',sent:'待确认',accepted:'已接受',declined:'已拒绝'};
const STATUS_COLOR = {draft:'#64748b',sent:'#92400e',accepted:'#065f46',declined:'#991b1b'};
const STATUS_BG = {draft:'#f1f5f9',sent:'#fef3c7',accepted:'#d1fae5',declined:'#fee2e2'};

async function load() {
    try {
        const r = await fetch('/api/my/offers', {headers:{'Accept':'application/json'}});
        const d = await r.json();
        const list = (d.data && d.data.list) ? d.data.list : [];
        if (!list.length) {
            document.getElementById('offerList').innerHTML = '<div class="card" style="text-align:center;padding:60px;color:#94a3b8;">暂无录用通知</div>';
            return;
        }
        document.getElementById('offerList').innerHTML = list.map(o => `
            <div class="card">
                <div style="display:flex;justify-content:space-between;align-items:flex-start;">
                    <div style="flex:1;">
                        <div style="font-size:18px;font-weight:600;color:#065f46;margin-bottom:4px;">${o.position}</div>
                        <div style="font-size:14px;color:#64748b;">${o.application?.job?.enterprise?.name || ''} | ${o.application?.job?.title || ''}</div>
                        <div style="margin-top:10px;display:flex;gap:24px;">
                            <div><span style="font-size:13px;color:#94a3b8;">薪资</span><div style="font-size:16px;font-weight:600;color:#ef4444;">${o.salary}</div></div>
                            <div><span style="font-size:13px;color:#94a3b8;">入职日期</span><div style="font-size:15px;color:#1e293b;">${o.start_date}</div></div>
                        </div>
                        ${o.note ? `<div style="margin-top:8px;font-size:13px;color:#64748b;background:#f8fafc;padding:4px 10px;border-radius:6px;">备注：${o.note}</div>` : ''}
                    </div>
                    <div style="text-align:right;margin-left:16px;">
                        <span style="display:inline-block;padding:2px 10px;border-radius:12px;font-size:12px;font-weight:600;color:${STATUS_COLOR[o.status]||'#64748b'};background:${STATUS_BG[o.status]||'#f1f5f9'};margin-bottom:8px;">${STATUS_MAP[o.status]||'未知'}</span>
                        ${o.status === 'sent' ? `
                            <div style="display:flex;gap:6px;flex-direction:column;">
                                <button onclick="respond(${o.id},'accept')" style="padding:6px 16px;background:#065f46;color:#fff;border:none;border-radius:6px;cursor:pointer;font-size:13px;font-weight:600;">接受 Offer</button>
                                <button onclick="respond(${o.id},'decline')" style="padding:6px 16px;background:#991b1b;color:#fff;border:none;border-radius:6px;cursor:pointer;font-size:13px;">拒绝</button>
                            </div>
                        ` : ''}
                    </div>
                </div>
            </div>
        `).join('');
    } catch(e) {
        document.getElementById('offerList').innerHTML = '<div class="card" style="text-align:center;padding:60px;color:#ef4444;">加载失败，请先<a href="/login">登录</a></div>';
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
