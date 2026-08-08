@extends('frontend.layout')

@section('title', '岗位搜索')

@section('content')
<div class="container" style="margin-top:24px;">
    <a href="/" class="btn-back mb-lg">&larr; 返回首页</a>

    <div class="card" style="padding:20px;margin-bottom:20px;">
        <div class="flex gap-sm" style="flex-wrap:wrap;">
            <div style="position:relative;flex:1;min-width:200px;">
                <input id="keyword" type="text" placeholder="岗位名称 / 技能关键词" class="form-input" style="width:100%;padding-right:32px;" oninput="toggleClear()" onkeydown="if(event.key==='Enter')search()">
                <button id="clearBtn" onclick="resetSearch()" title="清空" class="input-clear-btn">×</button>
            </div>
            <select id="city" class="form-input" style="width:140px;" onchange="search()"><option value="">全部城市</option></select>
            <select id="type" class="form-input" style="width:120px;" onchange="search()">
                <option value="">全部类型</option>
                <option value="full-time">全职</option>
                <option value="internship">实习</option>
            </select>
            <select id="education" class="form-input" style="width:120px;" onchange="search()">
                <option value="">全部学历</option>
            </select>
            <button onclick="search()" class="btn btn-primary" style="padding:8px 24px;">搜索</button>
        </div>
    </div>

    <div id="jobList"></div>
    <div id="pagination" class="text-center mt-xl"></div>
</div>

<script>
let currentPage = 1;

async function loadCities() {
    try {
        const r = await fetch('/api/jobs/cities', {headers:{'Accept':'application/json'}});
        const d = await r.json();
        if (d.code === 200 && d.data) {
            const sel = document.getElementById('city');
            d.data.forEach(c => { const o = document.createElement('option'); o.value = c; o.textContent = c; sel.appendChild(o); });
        }
    } catch(e) {}
}

async function search(page = 1) {
    currentPage = page;
    const params = new URLSearchParams();
    const keyword = document.getElementById('keyword').value.trim();
    const city = document.getElementById('city').value;
    const type = document.getElementById('type').value;
    const education = document.getElementById('education').value;
    if (keyword) params.set('keyword', keyword);
    if (city) params.set('city', city);
    if (type) params.set('type', type);
    if (education) params.set('education', education);
    params.set('page', page);

    try {
        const r = await fetch('/api/jobs?' + params.toString(), {headers:{'Accept':'application/json'}});
        const d = await r.json();
        if (d.code !== 200) return;
        const jobs = d.data.list;
        const meta = d.data.meta;
        let html = '';
        if (!jobs.length) {
            html = '<div class="card empty-state-card">暂无岗位</div>';
        } else {
            jobs.forEach(j => {
                html += `
                <a href="/jobs/${j.id}" class="card card-hover mb-md" style="display:block;text-decoration:none;color:inherit;">
                    <div class="flex" style="justify-content:space-between;align-items:flex-start;">
                        <div style="flex:1;">
                            <h3 class="text-xl mb-xs" style="font-weight:600;">${j.title}</h3>
                            <div class="mb-sm">
                                <span class="tag tag-blue">${j.enterprise?.name || ''}</span>
                                <span class="tag tag-gray ml-xs">${j.city}</span>
                                <span class="tag tag-yellow ml-xs">${j.type === 'full-time' ? '全职' : '实习'}</span>
                                <span class="tag ml-xs">${j.education}</span>
                            </div>
                            <div class="text-sm text-light">${j.skills || ''}</div>
                        </div>
                        <div class="text-right" style="white-space:nowrap;">
                            <div class="text-2xl font-bold text-danger">${(j.salary_min / 1000).toFixed(0)}k - ${(j.salary_max / 1000).toFixed(0)}k</div>
                            <div class="text-xs text-light">/月</div>
                        </div>
                    </div>
                </a>`;
            });
        }
        document.getElementById('jobList').innerHTML = html;

        renderPagination('pagination', currentPage, meta.last_page, 'search');
    } catch(e) {
        document.getElementById('jobList').innerHTML = '<div class="card empty-state-card text-danger">加载失败</div>';
    }
}

// Populate education options from common values
['中专','大专','本科','硕士','博士','不限'].forEach(e => {
    const o = document.createElement('option');
    o.value = e; o.textContent = e;
    document.getElementById('education').appendChild(o);
});

function toggleClear() {
    const btn = document.getElementById('clearBtn');
    btn.classList.toggle('visible', document.getElementById('keyword').value.trim());
}
function resetSearch() {
    document.getElementById('keyword').value = '';
    document.getElementById('city').value = '';
    document.getElementById('type').value = '';
    document.getElementById('education').value = '';
    document.getElementById('clearBtn').classList.remove('visible');
    search();
}

// 读取首页 hero 搜索传入的参数
(function(){
    const q = new URLSearchParams(location.search);
    if (q.get('keyword')) document.getElementById('keyword').value = q.get('keyword');
    if (q.get('city')) document.getElementById('city').value = q.get('city');
    if (q.get('type')) document.getElementById('type').value = q.get('type');
})();

loadCities();
search();
</script>
@endsection
