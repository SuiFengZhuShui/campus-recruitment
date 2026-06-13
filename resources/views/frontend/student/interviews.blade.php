@extends('frontend.layout')

@section('title', '我的面试')

@section('content')
<div class="container" style="margin-top:24px;">
    <h1 style="font-size:20px;margin-bottom:20px;">我的面试</h1>
    <div id="interviewList"></div>
</div>

<script>
const STATUS_MAP = {invited:'待确认',accepted:'已接受',declined:'已拒绝',completed:'已完成'};
const STATUS_COLOR = {invited:'#92400e',accepted:'#065f46',declined:'#991b1b',completed:'#1e40af'};
const STATUS_BG = {invited:'#fef3c7',accepted:'#d1fae5',declined:'#fee2e2',completed:'#dbeafe'};
const TYPE_MAP = {online:'线上','on-site':'线下'};

async function load() {
    try {
        const r = await fetch('/api/my/interviews', {headers:{'Accept':'application/json'}});
        const d = await r.json();
        const list = (d.data && d.data.list) ? d.data.list : [];
        if (!list.length) {
            document.getElementById('interviewList').innerHTML = '<div class="card" style="text-align:center;padding:60px;color:#94a3b8;">暂无面试安排</div>';
            return;
        }
        document.getElementById('interviewList').innerHTML = list.map(iv => `
            <div class="card">
                <div style="display:flex;justify-content:space-between;align-items:flex-start;">
                    <div style="flex:1;">
                        <div style="font-size:16px;font-weight:600;color:#1e293b;">${iv.application?.job?.title || '-'}</div>
                        <div style="font-size:14px;color:#64748b;margin-top:2px;">${iv.application?.job?.enterprise?.name || ''}</div>
                        <div style="margin-top:8px;font-size:14px;color:#475569;">
                            <span style="font-weight:500;">面试时间：</span>${new Date(iv.scheduled_at).toLocaleString('zh-CN')}
                        </div>
                        <div style="font-size:14px;color:#475569;">
                            <span style="font-weight:500;">地点：</span>${iv.location} <span class="tag tag-blue" style="margin-left:4px;">${TYPE_MAP[iv.type]||iv.type}</span>
                        </div>
                        ${iv.contact ? `<div style="font-size:14px;color:#475569;"><span style="font-weight:500;">联系人：</span>${iv.contact}</div>` : ''}
                        ${iv.note ? `<div style="margin-top:6px;font-size:13px;color:#64748b;background:#f8fafc;padding:4px 10px;border-radius:6px;">备注：${iv.note}</div>` : ''}
                    </div>
                    <div style="text-align:right;margin-left:16px;">
                        <span style="display:inline-block;padding:2px 10px;border-radius:12px;font-size:12px;font-weight:600;color:${STATUS_COLOR[iv.status]||'#64748b'};background:${STATUS_BG[iv.status]||'#f1f5f9'};margin-bottom:8px;">${STATUS_MAP[iv.status]||'未知'}</span>
                        ${iv.status === 'invited' ? `
                            <div style="display:flex;gap:6px;flex-direction:column;">
                                <button onclick="respond(${iv.id},'accept')" style="padding:4px 12px;background:#065f46;color:#fff;border:none;border-radius:6px;cursor:pointer;font-size:13px;">接受</button>
                                <button onclick="respond(${iv.id},'decline')" style="padding:4px 12px;background:#991b1b;color:#fff;border:none;border-radius:6px;cursor:pointer;font-size:13px;">拒绝</button>
                            </div>
                        ` : ''}
                    </div>
                </div>
            </div>
        `).join('');
    } catch(e) {
        document.getElementById('interviewList').innerHTML = '<div class="card" style="text-align:center;padding:60px;color:#ef4444;">加载失败，请先<a href="/login">登录</a></div>';
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
