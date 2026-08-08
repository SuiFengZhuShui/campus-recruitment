@extends('frontend.layout')

@section('title', '浏览学生简历')

@section('content')
<div class="container" style="margin-top:24px;">
    <a href="/enterprise/dashboard" class="btn-back mb-lg" onclick="return goBack('/enterprise/dashboard')">&larr; 返回企业后台</a>
    <h1 class="text-3xl mb-lg" style="font-weight:700;">浏览学生简历</h1>

    <div class="card" style="padding:20px;margin-bottom:20px;">
        <div class="flex gap-sm">
            <input id="search" type="text" placeholder="姓名 / 学号 / 班级" class="form-input" style="flex:1;max-width:300px;" onkeydown="if(event.key==='Enter')loadResumes()">
            <select id="college" class="form-input" style="width:180px;"><option value="">全部学院</option></select>
            <button onclick="loadResumes()" class="btn btn-primary" style="padding:8px 24px;">搜索</button>
        </div>
    </div>

    <div id="resumeList"></div>
    <div id="pagination" class="text-center mt-xl"></div>
</div>

<script>
let currentPage = 1;

async function loadColleges() {
    const seen = new Set();
    // Populate colleges from known data — fetch from resumes to get distinct colleges
    try {
        const r = await fetch('/api/my/resumes?per_page=1', {headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest'}});
        if (r.status === 403) { location.href = '/enterprise/waiting'; return; }
    } catch(e) {}
    // For now, colleges will load dynamically from the list
}

async function loadResumes(page = 1) {
    currentPage = page;
    const params = new URLSearchParams();
    const search = document.getElementById('search').value.trim();
    const collegeId = document.getElementById('college').value;
    if (search) params.set('search', search);
    if (collegeId) params.set('college_id', collegeId);
    params.set('page', page);

    try {
        const r = await fetch('/api/my/resumes?' + params.toString(), {headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest'}});
        if (r.status === 401) { location.href = '/login'; return; }
        if (r.status === 403) { location.href = '/enterprise/waiting'; return; }
        const d = await r.json();
        if (d.code !== 200) return;

        const students = d.data.list;
        const meta = d.data.meta;
        let html = '';

        if (!students.length) {
            html = '<div class="card empty-state-card">暂无学生简历</div>';
        } else {
            students.forEach(s => {
                html += `
                <div class="card mb-sm" style="padding:16px;">
                    <div class="flex" style="justify-content:space-between;align-items:center;">
                        <div>
                            <strong class="text-lg">${s.user?.name || '-'}</strong>
                            <span class="text-sm text-light ml-sm">${s.student_no}</span>
                            <span class="tag ml-sm">${s.class_name || ''}</span>
                            <span class="tag tag-blue ml-xs">${s.college?.name || ''}</span>
                            <span class="tag tag-yellow ml-xs">${s.grade || ''}级</span>
                        </div>
                        <div>
                            ${s.resume_path ? `<a href="/api/resume/${s.id}" target="_blank" class="btn btn-primary btn-sm">下载简历</a>` : '<span class="text-sm text-light">未上传</span>'}
                        </div>
                    </div>
                </div>`;
            });
        }

        document.getElementById('resumeList').innerHTML = html;

        // Collect colleges
        students.forEach(s => {
            if (s.college) {
                const sel = document.getElementById('college');
                if (![...sel.options].some(o => o.value == s.college.id)) {
                    const opt = document.createElement('option');
                    opt.value = s.college.id;
                    opt.textContent = s.college.name;
                    sel.appendChild(opt);
                }
            }
        });

        renderPagination('pagination', currentPage, meta.last_page, 'loadResumes');
    } catch(e) {
        document.getElementById('resumeList').innerHTML = '<div class="card empty-state-card text-danger">加载失败</div>';
    }
}

loadResumes();
</script>
@endsection
