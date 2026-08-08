@extends('frontend.layout')

@section('title', '岗位搜索 - ' . config('school.short_name'))

@section('content')
<div class="hero">
    <div class="container">
        <h1>找一份好工作</h1>
        <p>搜索校园招聘岗位，一键投递</p>
        <div class="hero-search">
            <div style="position:relative;flex:1;min-width:200px;">
                <input id="searchKeyword" type="text" placeholder="岗位名称/技能关键词" style="width:100%;padding-right:32px;" oninput="toggleClearBtn()" onkeydown="if(event.key==='Enter')searchJobs()">
                <button id="clearSearch" onclick="clearSearch()" title="清空" class="input-clear-btn">×</button>
            </div>
            <select id="searchCity" onchange="searchJobs()">
                <option value="">全部城市</option>
            </select>
            <select id="searchType" onchange="searchJobs()">
                <option value="">全部类型</option>
                <option value="full-time">全职</option>
                <option value="internship">实习</option>
            </select>
            <button onclick="searchJobs()" class="btn btn-primary" style="padding:10px 28px;font-size:15px;">搜索</button>
        </div>
    </div>
</div>

@if (auth()->check() && auth()->user()->isStudent())
    <div class="container" style="margin-top:12px;">
        <div class="student-notice-bar" style="background:linear-gradient(135deg,#fef3c7,#fde68a);color:#92400e;padding:10px 20px;border-radius:8px;text-align:center;font-size:14px;font-weight:500;overflow:hidden;white-space:nowrap;">
            <span style="display:inline-block;animation:scrollLeft 16s linear infinite;">📢 温馨提示：先上传简历，上传简历后点击投递企业才能看到你的简历！为你的求职之路做好准备吧！</span>
        </div>
    </div>
    <style>
    @keyframes scrollLeft {
        0% { transform: translateX(60%); }
        100% { transform: translateX(-60%); }
    }
    </style>
@endif

<div class="container mt-2xl">
    <div id="jobList" class="job-grid" style="display:grid;grid-template-columns:repeat(3,1fr);gap:18px;">
        <div class="job-skeleton" style="height:140px;background:#f1f5f9;border-radius:10px;animation:pulse 1.2s ease-in-out infinite;"></div>
        <div class="job-skeleton" style="height:140px;background:#f1f5f9;border-radius:10px;animation:pulse 1.2s ease-in-out infinite .2s;"></div>
        <div class="job-skeleton" style="height:140px;background:#f1f5f9;border-radius:10px;animation:pulse 1.2s ease-in-out infinite .4s;"></div>
    </div>
    <div id="pagination" class="text-center mt-xl"></div>
</div>
<style>
@keyframes pulse { 0%,100%{opacity:1} 50%{opacity:.4} }
</style>

<script>
const API = '/api';

function toggleClearBtn() {
    const btn = document.getElementById('clearSearch');
    btn.classList.toggle('visible', document.getElementById('searchKeyword').value.trim());
}

function clearSearch() {
    document.getElementById('searchKeyword').value = '';
    document.getElementById('clearSearch').classList.remove('visible');
    document.getElementById('searchCity').value = '';
    document.getElementById('searchType').value = '';
    searchJobs();
}

async function loadCities() {
    try {
        const r = await fetch(API + '/jobs/cities', {headers:{'Accept':'application/json'}});
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
        const r = await fetch(API + '/jobs?' + params, {headers:{'Accept':'application/json'}});
        const d = await r.json();
        const list = d.data.list || [];
        const meta = d.data.meta || {};

        const container = document.getElementById('jobList');
        if (!list.length) {
            container.style.display = 'block';
            container.innerHTML = '<div class="empty-state" style="grid-column:1/-1;">暂无匹配岗位</div>';
        } else {
            container.style.display = 'grid';
            container.innerHTML = list.map(j => `
                <a href="/jobs/${j.id}" class="card job-card" style="display:block;text-decoration:none;color:inherit;">
                    <div class="flex-between-start">
                        <div style="flex:1;min-width:0;">
                            <div class="job-card-title">${j.title}</div>
                            <div class="job-card-meta">
                                <span class="tag tag-green">${j.enterprise?.name || ''}</span>
                                <span class="tag tag-gray">${j.city}</span>
                                <span class="tag tag-blue">${j.education}</span>
                                <span class="tag tag-yellow">${j.type === 'full-time' ? '全职' : '实习'}</span>
                            </div>
                            <div class="job-card-meta">${(j.skills || '').split(',').filter(Boolean).map(s => `<span class="tag tag-gray">${s.trim()}</span>`).join('')}</div>
                        </div>
                        <div class="text-right" style="flex-shrink:0;">
                            <div class="text-xl font-bold text-danger">${j.salary_min / 1000}k-${j.salary_max / 1000}k<span class="text-xs text-light" style="font-weight:400;"> /月</span></div>
                        </div>
                    </div>
                </a>
            `).join('');
        }

        renderPagination('pagination', page, meta.last_page, 'searchJobs');
    } catch(e) {
        document.getElementById('jobList').innerHTML = '<div class="card empty-state-card text-danger">加载失败</div>';
    }
}

loadCities();
searchJobs();
</script>
@endsection
