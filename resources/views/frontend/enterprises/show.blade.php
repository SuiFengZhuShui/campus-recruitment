@extends('frontend.layout')

@section('title', '企业详情')

@section('content')
<div class="container" style="margin-top:24px;">
    <div id="enterpriseDetail"></div>
</div>

<script>
const enterpriseId = window.location.pathname.split('/').pop();

async function loadEnterprise() {
    try {
        const r = await fetch('/api/enterprises/' + enterpriseId, {headers:{'Accept':'application/json'}});
        const d = await r.json();
        if (d.code !== 200) {
            document.getElementById('enterpriseDetail').innerHTML = '<div class="card empty-state-card text-danger">企业不存在</div>';
            return;
        }
        const e = d.data;
        let jobsHtml = '';
        if (e.jobs && e.jobs.length) {
            jobsHtml = '<div class="card"><h3 class="section-title">在招岗位</h3>';
            e.jobs.forEach(j => {
                jobsHtml += `
                <a href="/jobs/${j.id}" class="card card-hover mb-sm" style="display:block;text-decoration:none;color:inherit;border:1px solid var(--color-border);">
                    <div class="flex" style="justify-content:space-between;">
                        <div>
                            <strong>${j.title}</strong> &middot; ${j.city} &middot; ${j.education}
                        </div>
                        <div class="text-danger" style="font-weight:600;">${(j.salary_min / 1000).toFixed(0)}k-${(j.salary_max / 1000).toFixed(0)}k</div>
                    </div>
                </a>`;
            });
            jobsHtml += '</div>';
        }

        document.getElementById('enterpriseDetail').innerHTML = `
            <a href="/enterprises" class="btn-back mb-lg">&larr; 返回企业列表</a>
            <div class="card">
                <h1 class="text-3xl mb-sm">${e.name}</h1>
                <div class="mb-lg">
                    <span class="tag tag-yellow">${e.industry || '-'}</span>
                    <span class="tag ml-sm">${e.scale || ''}</span>
                    ${e.college ? `<span class="tag tag-blue ml-sm">${e.college.name}</span>` : ''}
                </div>
                ${e.intro ? `<div class="text-base" style="line-height:1.8;white-space:pre-wrap;">${e.intro}</div>` : ''}
            </div>
            ${jobsHtml}
        `;
    } catch(e) {
        document.getElementById('enterpriseDetail').innerHTML = '<div class="card empty-state-card text-danger">加载失败</div>';
    }
}

loadEnterprise();
</script>
@endsection
