@extends('frontend.layout')

@section('title', '岗位搜索 - 校园招聘平台')

@section('content')
<div style="background:linear-gradient(135deg,#1e40af,#3b82f6);padding:40px 24px;color:#fff;">
    <div class="container">
        <h1 style="font-size:24px;margin-bottom:8px;">找一份好工作</h1>
        <p style="font-size:14px;color:#bfdbfe;">搜索校园招聘岗位，一键投递</p>
        <div style="display:flex;gap:8px;margin-top:16px;flex-wrap:wrap;">
            <input id="searchKeyword" type="text" placeholder="岗位名称/技能关键词" style="flex:1;min-width:200px;padding:10px 14px;border:none;border-radius:8px;font-size:15px;">
            <select id="searchCity" style="padding:10px 14px;border:none;border-radius:8px;font-size:14px;min-width:120px;">
                <option value="">全部城市</option>
            </select>
            <select id="searchType" style="padding:10px 14px;border:none;border-radius:8px;font-size:14px;">
                <option value="">全部类型</option>
                <option value="full-time">全职</option>
                <option value="internship">实习</option>
            </select>
            <button onclick="searchJobs()" class="btn btn-primary" style="padding:10px 28px;font-size:15px;">搜索</button>
        </div>
    </div>
</div>

<div class="container" style="margin-top:24px;">
    <div id="jobList"></div>
    <div id="pagination" style="text-align:center;margin-top:20px;"></div>
</div>

<script>
const API = '/api';

async function loadCities() {
    try {
        const r = await fetch(API + '/jobs/cities');
        const data = await r.json();
        const sel = document.getElementById('searchCity');
        (data.data || []).forEach(c => { const o = document.createElement('option'); o.value = c; o.textContent = c; sel.appendChild(o); });
    } catch(e) {}
}

async function searchJobs(page = 1) {
    const params = new URLSearchParams();
    const kw = document.getElementById('searchKeyword').value.trim();
    const city = document.getElementById('searchCity').value;
    const type = document.getElementById('searchType').value;
    if (kw) params.set('keyword', kw);
    if (city) params.set('city', city);
    if (type) params.set('type', type);
    params.set('page', page);

    try {
        const r = await fetch(API + '/jobs?' + params);
        const d = await r.json();
        const list = d.data.list || [];
        const meta = d.data.meta || {};

        const container = document.getElementById('jobList');
        if (!list.length) {
            container.innerHTML = '<div class="card" style="text-align:center;padding:60px;color:#94a3b8;">暂无匹配岗位</div>';
        } else {
            container.innerHTML = list.map(j => `
                <div class="card" style="display:flex;justify-content:space-between;align-items:flex-start;">
                    <div style="flex:1;">
                        <a href="/jobs/${j.id}" style="font-size:17px;font-weight:600;color:#1e293b;text-decoration:none;">${j.title}</a>
                        <div style="margin-top:8px;font-size:14px;color:#475569;">
                            <span class="tag tag-green">${j.enterprise?.name || ''}</span>
                            <span class="tag tag-gray" style="margin-left:8px;">${j.city}</span>
                            <span class="tag tag-blue" style="margin-left:8px;">${j.education}</span>
                            <span class="tag tag-yellow" style="margin-left:8px;">${j.type === 'full-time' ? '全职' : '实习'}</span>
                        </div>
                        <div style="margin-top:8px;font-size:14px;color:#94a3b8;">${(j.skills || '').split(',').filter(Boolean).map(s => `<span class="tag tag-gray" style="margin-right:4px;">${s.trim()}</span>`).join('')}</div>
                    </div>
                    <div style="text-align:right;">
                        <div style="font-size:18px;font-weight:700;color:#ef4444;">${j.salary_min / 1000}k-${j.salary_max / 1000}k</div>
                        <div style="font-size:12px;color:#94a3b8;margin-top:4px;">/ 月</div>
                    </div>
                </div>
            `).join('');
        }

        // Pagination
        const pg = document.getElementById('pagination');
        if (meta.last_page > 1) {
            let html = '';
            for (let i = 1; i <= meta.last_page; i++) {
                html += `<button onclick="searchJobs(${i})" style="padding:6px 14px;border:1px solid ${i===page?'#3b82f6':'#e2e8f0'};background:${i===page?'#3b82f6':'#fff'};color:${i===page?'#fff':'#475569'};border-radius:6px;margin:0 4px;cursor:pointer;font-size:13px;">${i}</button>`;
            }
            pg.innerHTML = html;
        } else {
            pg.innerHTML = '';
        }
    } catch(e) {
        document.getElementById('jobList').innerHTML = '<div class="card" style="text-align:center;padding:60px;color:#ef4444;">加载失败</div>';
    }
}

loadCities();
searchJobs();
</script>
@endsection
