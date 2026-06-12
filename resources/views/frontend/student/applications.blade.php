@extends('frontend.layout')

@section('title', '我的投递')

@section('content')
<div class="container" style="margin-top:24px;">
    <h1 style="font-size:20px;margin-bottom:20px;">我的投递</h1>
    <div id="appList"></div>
</div>

<script>
async function load() {
    try {
        const r = await fetch('/api/my/applications', {headers:{'Accept':'application/json'}});
        const d = await r.json();
        const list = (d.data && d.data.list) ? d.data.list : [];
        if (!list.length) {
            document.getElementById('appList').innerHTML = '<div class="card" style="text-align:center;padding:60px;color:#94a3b8;">还没有投递任何岗位，<a href="/">去看看</a></div>';
            return;
        }
        document.getElementById('appList').innerHTML = list.map(a => `
            <div class="card">
                <div style="display:flex;justify-content:space-between;align-items:center;">
                    <div style="flex:1;">
                        <a href="/jobs/${a.job?.id}" style="font-size:16px;font-weight:600;color:#1e293b;text-decoration:none;">${a.job?.title || '-'}</a>
                        <div style="margin-top:4px;">
                            <span class="tag tag-green">${a.job?.enterprise?.name || ''}</span>
                            <span class="tag tag-gray" style="margin-left:8px;">${a.job?.city || ''}</span>
                            <span class="tag tag-yellow" style="margin-left:8px;">${a.job?.type === 'full-time' ? '全职' : '实习'}</span>
                        </div>
                    </div>
                    <div style="text-align:right;">
                        <div style="font-size:13px;color:#94a3b8;">投递时间</div>
                        <div style="font-size:14px;">${new Date(a.created_at).toLocaleDateString('zh-CN')}</div>
                    </div>
                </div>
            </div>
        `).join('');
    } catch(e) {
        document.getElementById('appList').innerHTML = '<div class="card" style="text-align:center;padding:60px;color:#ef4444;">加载失败，请先<a href="/login">登录</a></div>';
    }
}
load();
</script>
@endsection
