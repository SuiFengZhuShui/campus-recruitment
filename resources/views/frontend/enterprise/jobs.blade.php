@extends('frontend.layout')

@section('title', '我的岗位')

@section('content')
<div class="container" style="margin-top:24px;">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
        <h1 style="font-size:20px;">我的岗位</h1>
        <button onclick="showCreateForm()" class="btn btn-primary">+ 发布岗位</button>
    </div>

    <div id="flash" style="display:none;"></div>
    <div id="jobList"></div>
</div>

<!-- Create/Edit Modal -->
<div id="jobModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.4);z-index:100;overflow-y:auto;padding:20px;">
    <div style="background:#fff;max-width:600px;margin:40px auto;border-radius:12px;padding:28px;">
        <h2 id="modalTitle" style="margin-bottom:20px;">发布岗位</h2>
        <form id="jobForm" onsubmit="saveJob(event)">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div><label style="font-size:13px;color:#475569;">岗位名称 *</label><input type="text" name="title" id="fTitle" required maxlength="200" style="width:100%;padding:9px 12px;border:1px solid #cbd5e1;border-radius:6px;font-size:14px;"></div>
                <div><label style="font-size:13px;color:#475569;">招聘人数 *</label><input type="number" name="count" id="fCount" required min="1" style="width:100%;padding:9px 12px;border:1px solid #cbd5e1;border-radius:6px;font-size:14px;"></div>
                <div><label style="font-size:13px;color:#475569;">工作城市 *</label><input type="text" name="city" id="fCity" required maxlength="50" style="width:100%;padding:9px 12px;border:1px solid #cbd5e1;border-radius:6px;font-size:14px;"></div>
                <div><label style="font-size:13px;color:#475569;">类型 *</label><select name="type" id="fType" style="width:100%;padding:9px 12px;border:1px solid #cbd5e1;border-radius:6px;font-size:14px;"><option value="full-time">全职</option><option value="internship">实习</option></select></div>
                <div><label style="font-size:13px;color:#475569;">薪资下限(k) *</label><input type="number" name="salary_min" id="fSalaryMin" required min="0" style="width:100%;padding:9px 12px;border:1px solid #cbd5e1;border-radius:6px;font-size:14px;"></div>
                <div><label style="font-size:13px;color:#475569;">薪资上限(k) *</label><input type="number" name="salary_max" id="fSalaryMax" required min="0" style="width:100%;padding:9px 12px;border:1px solid #cbd5e1;border-radius:6px;font-size:14px;"></div>
                <div><label style="font-size:13px;color:#475569;">学历要求 *</label><input type="text" name="education" id="fEducation" required maxlength="30" style="width:100%;padding:9px 12px;border:1px solid #cbd5e1;border-radius:6px;font-size:14px;"></div>
                <div><label style="font-size:13px;color:#475569;">专业要求</label><input type="text" name="major" id="fMajor" maxlength="200" style="width:100%;padding:9px 12px;border:1px solid #cbd5e1;border-radius:6px;font-size:14px;"></div>
                <div style="grid-column:1/-1;"><label style="font-size:13px;color:#475569;">技能标签（逗号分隔）</label><input type="text" name="skills" id="fSkills" maxlength="500" style="width:100%;padding:9px 12px;border:1px solid #cbd5e1;border-radius:6px;font-size:14px;"></div>
                <div style="grid-column:1/-1;"><label style="font-size:13px;color:#475569;">岗位职责 *</label><textarea name="duty" id="fDuty" required rows="3" style="width:100%;padding:9px 12px;border:1px solid #cbd5e1;border-radius:6px;font-size:14px;resize:vertical;"></textarea></div>
                <div style="grid-column:1/-1;"><label style="font-size:13px;color:#475569;">任职要求 *</label><textarea name="requirement" id="fRequirement" required rows="3" style="width:100%;padding:9px 12px;border:1px solid #cbd5e1;border-radius:6px;font-size:14px;resize:vertical;"></textarea></div>
                <div style="grid-column:1/-1;"><label style="font-size:13px;color:#475569;">福利待遇</label><textarea name="welfare" id="fWelfare" rows="2" style="width:100%;padding:9px 12px;border:1px solid #cbd5e1;border-radius:6px;font-size:14px;resize:vertical;"></textarea></div>
            </div>
            <input type="hidden" name="_method" id="fMethod" value="POST">
            <input type="hidden" name="job_id" id="fJobId">
            <div style="display:flex;gap:8px;margin-top:16px;">
                <button type="submit" class="btn btn-primary" style="flex:1;justify-content:center;">保存</button>
                <button type="button" onclick="closeModal()" class="btn btn-outline" style="flex:1;justify-content:center;">取消</button>
            </div>
        </form>
    </div>
</div>

<script>
const API = '/api/my/jobs';

async function loadJobs() {
    try {
        const r = await fetch(API, {headers:{'Accept':'application/json'}});
        const d = await r.json();
        const list = (d.data && d.data.list) ? d.data.list : [];
        const c = document.getElementById('jobList');
        if (!list.length) {
            c.innerHTML = '<div class="card" style="text-align:center;padding:60px;color:#94a3b8;">暂无岗位，点击上方按钮发布</div>';
            return;
        }
        c.innerHTML = list.map(j => `
            <div class="card" style="display:flex;justify-content:space-between;align-items:center;">
                <div style="flex:1;">
                    <div style="font-size:16px;font-weight:600;">${j.title}</div>
                    <div style="margin-top:4px;font-size:13px;color:#64748b;">${j.city} · ${j.education} · ${j.salary_min/1000}k-${j.salary_max/1000}k · ${j.type==='full-time'?'全职':'实习'} · <span class="tag ${j.status==='active'?'tag-green':'tag-gray'}">${j.status==='active'?'上架':'下架'}</span></div>
                </div>
                <div style="display:flex;gap:8px;">
                    <a href="/enterprise/jobs/${j.id}/applications" class="btn btn-outline" style="font-size:13px;padding:6px 12px;">查看投递</a>
                    <button onclick="editJob(${j.id})" class="btn btn-outline" style="font-size:13px;padding:6px 12px;">编辑</button>
                    <button onclick="toggleJob(${j.id})" class="btn ${j.status==='active'?'btn-outline':'btn-success'}" style="font-size:13px;padding:6px 12px;">${j.status==='active'?'下架':'上架'}</button>
                    <button onclick="deleteJob(${j.id})" class="btn btn-danger" style="font-size:13px;padding:6px 12px;">删除</button>
                </div>
            </div>
        `).join('');
    } catch(e) {
        document.getElementById('jobList').innerHTML = '<div class="card" style="text-align:center;padding:60px;color:#ef4444;">加载失败，请先<a href="/login">登录</a></div>';
    }
}

function showCreateForm() {
    document.getElementById('modalTitle').textContent = '发布岗位';
    document.getElementById('fMethod').value = 'POST';
    document.getElementById('fJobId').value = '';
    ['fTitle','fCount','fCity','fSalaryMin','fSalaryMax','fEducation','fMajor','fSkills','fDuty','fRequirement','fWelfare'].forEach(id => document.getElementById(id).value = '');
    document.getElementById('jobModal').style.display = 'block';
}

async function editJob(id) {
    try {
        const r = await fetch(API);
        const d = await r.json();
        const j = (d.data.list || []).find(x => x.id === id);
        if (!j) return;
        document.getElementById('modalTitle').textContent = '编辑岗位';
        document.getElementById('fMethod').value = 'PUT';
        document.getElementById('fJobId').value = id;
        document.getElementById('fTitle').value = j.title;
        document.getElementById('fCount').value = j.count;
        document.getElementById('fCity').value = j.city;
        document.getElementById('fType').value = j.type;
        document.getElementById('fSalaryMin').value = j.salary_min / 1000;
        document.getElementById('fSalaryMax').value = j.salary_max / 1000;
        document.getElementById('fEducation').value = j.education;
        document.getElementById('fMajor').value = j.major || '';
        document.getElementById('fSkills').value = j.skills || '';
        document.getElementById('fDuty').value = j.duty;
        document.getElementById('fRequirement').value = j.requirement;
        document.getElementById('fWelfare').value = j.welfare || '';
        document.getElementById('jobModal').style.display = 'block';
    } catch(e) {}
}

function closeModal() { document.getElementById('jobModal').style.display = 'none'; }

async function saveJob(e) {
    e.preventDefault();
    const id = document.getElementById('fJobId').value;
    const method = document.getElementById('fMethod').value;
    const url = method === 'PUT' ? API + '/' + id : API;

    const fd = new FormData();
    fd.append('_method', method);
    ['title','count','city','type','salary_min','salary_max','education','major','skills','duty','requirement','welfare'].forEach(f => {
        const v = document.getElementById('f' + f.charAt(0).toUpperCase() + f.slice(1)).value;
        if (v) fd.append(f, v);
    });

    try {
        const r = await fetch(url, { method:'POST', body:fd, headers:{'X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]').content} });
        const d = await r.json();
        if (d.code === 200) { closeModal(); loadJobs(); } else { alert(d.message || '保存失败'); }
    } catch(err) { alert('网络错误'); }
}

async function toggleJob(id) {
    try {
        const r = await fetch(API + '/' + id + '/toggle', { method:'POST', headers:{'X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]').content} });
        const d = await r.json();
        if (d.code === 200) loadJobs(); else alert(d.message);
    } catch(e) { alert('网络错误'); }
}

async function deleteJob(id) {
    if (!confirm('确定删除此岗位？')) return;
    try {
        await fetch(API + '/' + id, { method:'DELETE', headers:{'X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]').content} });
        loadJobs();
    } catch(e) { alert('网络错误'); }
}

loadJobs();
</script>
@endsection
