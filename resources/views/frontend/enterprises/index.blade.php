@extends('frontend.layout')

@section('title', '企业列表')

@section('content')
<div class="container" style="margin-top:24px;">
    <a href="/" class="btn-back mb-lg">&larr; 返回首页</a>
    <h1 class="text-3xl mb-lg" style="font-weight:700;">合作企业</h1>

    <div class="card" style="padding:20px;margin-bottom:20px;">
        <div class="flex gap-sm">
            <div style="position:relative;flex:1;">
                <input id="keyword" type="text" placeholder="企业名称 / 行业" class="form-input" style="width:100%;padding-right:32px;" oninput="toggleClear()" onkeydown="if(event.key==='Enter')search()">
                <button id="clearBtn" onclick="resetSearch()" title="清空" class="input-clear-btn">×</button>
            </div>
            <select id="industry" class="form-input" style="width:160px;" onchange="search()"><option value="">全部行业</option></select>
            <button onclick="search()" class="btn btn-primary" style="padding:8px 24px;">搜索</button>
        </div>
    </div>

    <div id="enterpriseList"></div>
    <div id="pagination" class="text-center mt-xl"></div>
</div>

<script>
let currentPage = 1;

async function loadIndustries() {
    try {
        const r = await fetch('/api/enterprises/industries', {headers:{'Accept':'application/json'}});
        const d = await r.json();
        if (d.code === 200 && d.data) {
            const sel = document.getElementById('industry');
            d.data.forEach(ind => { const o = document.createElement('option'); o.value = ind; o.textContent = ind; sel.appendChild(o); });
        }
    } catch(e) {}
}

async function search(page = 1) {
    currentPage = page;
    const params = new URLSearchParams();
    const keyword = document.getElementById('keyword').value.trim();
    const industry = document.getElementById('industry').value;
    if (keyword) params.set('keyword', keyword);
    if (industry) params.set('industry', industry);
    params.set('page', page);

    try {
        const r = await fetch('/api/enterprises?' + params.toString(), {headers:{'Accept':'application/json'}});
        const d = await r.json();
        if (d.code !== 200) return;
        const enterprises = d.data.list;
        const meta = d.data.meta;
        let html = '';
        if (!enterprises.length) {
            html = '<div class="card empty-state-card">暂无企业</div>';
        } else {
            enterprises.forEach(e => {
                html += `
                <a href="/enterprises/${e.id}" class="card card-hover mb-md" style="display:block;text-decoration:none;color:inherit;">
                    <h3 class="text-xl mb-sm" style="font-weight:600;">${e.name}</h3>
                    <div class="mb-xs">
                        <span class="tag tag-yellow">${e.industry || '-'}</span>
                        <span class="tag ml-xs">${e.scale || ''}</span>
                    </div>
                    <div class="text-sm text-light">${e.intro ? e.intro.substring(0, 100) + (e.intro.length > 100 ? '...' : '') : ''}</div>
                </a>`;
            });
        }
        document.getElementById('enterpriseList').innerHTML = html;

        let pager = '';
        if (meta.last_page > 1) {
            pager += '<div class="pagination">';
            for (let i = 1; i <= meta.last_page; i++) {
                pager += `<button onclick="search(${i})" class="pagination-btn${i === currentPage ? ' active' : ''}" type="button">${i}</button>`;
            }
            pager += '</div>';
        }
        document.getElementById('pagination').innerHTML = pager;
    } catch(e) {
        document.getElementById('enterpriseList').innerHTML = '<div class="card empty-state-card text-danger">加载失败</div>';
    }
}

function toggleClear() {
    const btn = document.getElementById('clearBtn');
    btn.classList.toggle('visible', document.getElementById('keyword').value.trim());
}
function resetSearch() {
    document.getElementById('keyword').value = '';
    document.getElementById('industry').value = '';
    document.getElementById('clearBtn').classList.remove('visible');
    search();
}

loadIndustries();
search();
</script>
@endsection
