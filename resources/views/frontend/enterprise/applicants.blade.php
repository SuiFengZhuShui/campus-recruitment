@extends('frontend.layout')

@section('title', '投递列表')

@section('content')
<div class="container mt-2xl">
    <a href="/enterprise/jobs" class="btn-back mb-lg" onclick="return goBack('/enterprise/jobs')">&larr; 返回岗位管理</a>
    <h1 class="page-title" id="jobTitle">投递列表</h1>
    <div id="appList"></div>

    <!-- Interview Modal -->
    <div id="interviewModal" class="modal-overlay" style="background:rgba(0,0,0,.5);z-index:1000;">
        <div class="card" style="width:100%;max-width:480px;padding:24px;">
            <h3 class="section-title">安排面试</h3>
            <form id="interviewForm" onsubmit="doScheduleInterview(event)">
                <input type="hidden" id="ivAppId">
                <div class="form-group">
                    <label class="form-label">面试时间</label>
                    <input type="datetime-local" name="scheduled_at" required class="form-input">
                </div>
                <div class="form-group">
                    <label class="form-label">面试方式</label>
                    <select name="type" class="form-input">
                        <option value="on-site">线下面试</option>
                        <option value="online">线上面试</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">地点/会议链接</label>
                    <input type="text" name="location" required class="form-input" placeholder="会议室地址或线上链接">
                </div>
                <div class="form-group">
                    <label class="form-label">联系人（选填）</label>
                    <input type="text" name="contact" class="form-input" placeholder="联系人姓名/电话">
                </div>
                <div class="form-group">
                    <label class="form-label">备注（选填）</label>
                    <textarea name="note" rows="2" class="form-input" placeholder="注意事项"></textarea>
                </div>
                <div class="flex gap-sm" style="justify-content:flex-end;">
                    <button type="button" onclick="closeInterviewModal()" class="btn" style="background:#e2e8f0;">取消</button>
                    <button type="submit" class="btn btn-primary">发送邀请</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Offer Modal -->
    <div id="offerModal" class="modal-overlay" style="background:rgba(0,0,0,.5);z-index:1000;">
        <div class="card" style="width:100%;max-width:480px;padding:24px;">
            <h3 class="section-title">发录用通知</h3>
            <form id="offerForm" onsubmit="doSendOffer(event)">
                <input type="hidden" id="ofAppId">
                <div class="form-group">
                    <label class="form-label">录用岗位</label>
                    <input type="text" name="position" required class="form-input" placeholder="正式岗位名称">
                </div>
                <div class="form-group">
                    <label class="form-label">薪资待遇</label>
                    <input type="text" name="salary" required class="form-input" placeholder="如：8K-12K，14薪，五险一金">
                </div>
                <div class="form-group">
                    <label class="form-label">入职日期</label>
                    <input type="date" name="start_date" required class="form-input">
                </div>
                <div class="form-group">
                    <label class="form-label">备注（选填）</label>
                    <textarea name="note" rows="2" class="form-input" placeholder="报到地址、携带材料等"></textarea>
                </div>
                <div class="flex gap-sm" style="justify-content:flex-end;">
                    <button type="button" onclick="closeOfferModal()" class="btn" style="background:#e2e8f0;">取消</button>
                    <button type="submit" class="btn btn-primary">发送录用通知</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
const jobId = window.location.pathname.split('/')[3];
const STATUS_MAP = {pending:'待审核',reviewed:'已查看',interviewed:'面试中',accepted:'已录用',rejected:'未通过'};
const IV_STATUS_MAP = {invited:'待确认',accepted:'已接受',declined:'已拒绝',completed:'已完成'};

async function load() {
    try {
        const r = await fetch('/api/my/jobs/' + jobId + '/applications', {headers:{'Accept':'application/json'}});
        const d = await r.json();
        if (d.code !== 200) { document.getElementById('appList').innerHTML = '<div class="card empty-state-card text-danger">加载失败</div>'; return; }
        document.getElementById('jobTitle').textContent = (d.data.job?.title || '岗位') + ' — 投递列表';
        const list = d.data.list || [];
        if (!list.length) {
            document.getElementById('appList').innerHTML = '<div class="card empty-state-card">暂无投递</div>';
            return;
        }
        document.getElementById('appList').innerHTML = list.map(a => {
            const statusColors = {pending:'#92400e',reviewed:'#1e40af',interviewed:'#7c3aed',accepted:'#065f46',rejected:'#991b1b'};
            const statusBgs = {pending:'#fef3c7',reviewed:'#dbeafe',interviewed:'#ede9fe',accepted:'#d1fae5',rejected:'#fee2e2'};
            return `
            <div class="card">
                <div class="flex-between-start">
                    <div style="flex:1;">
                        <strong>${a.student?.user?.name || '-'}</strong>
                        <span class="tag tag-blue ml-sm">${a.student?.student_no || ''}</span>
                        <span class="tag tag-gray ml-sm">${a.student?.class_name || ''}</span>
                        <span style="display:inline-block;margin-left:8px;padding:2px 10px;border-radius:12px;font-size:12px;font-weight:600;color:${statusColors[a.status]||'#64748b'};background:${statusBgs[a.status]||'#f1f5f9'};">${STATUS_MAP[a.status]||'未知'}</span>
                        ${a.interview ? `<span style="display:inline-block;margin-left:4px;padding:2px 8px;border-radius:12px;font-size:11px;font-weight:600;color:${a.interview.status==='accepted'?'#065f46':'#92400e'};background:${a.interview.status==='accepted'?'#d1fae5':'#fef3c7'};">面试：${IV_STATUS_MAP[a.interview.status]||a.interview.status}</span>` : ''}
                        <div class="text-base text-muted mt-sm">
                            手机：${a.student?.user?.phone || '-'} | 邮箱：${a.student?.user?.email || '-'}
                            ${a.student?.resume_path ? `<a href="/api/resume/${a.student?.id}" class="text-primary ml-md">下载简历</a>` : '<span class="text-light ml-sm">未上传简历</span>'}
                        </div>
                        ${a.remark ? `<div class="note-box note-box-md mt-sm">备注：${a.remark}</div>` : ''}
                    </div>
                    <div class="text-right ml-lg">
                        <div class="text-sm text-light mb-sm">${new Date(a.created_at).toLocaleDateString('zh-CN')}</div>
                        ${a.status !== 'rejected' ? `
                        <select onchange="updateStatus(${a.id}, this.value)" class="form-input" style="padding:4px 8px;font-size:13px;">
                            <option value="">- 修改状态 -</option>
                            <option value="reviewed" ${a.status==='reviewed'?'selected':''}>已查看</option>
                            <option value="interviewed" ${a.status==='interviewed'?'selected':''}>面试中</option>
                            <option value="accepted" ${a.status==='accepted'?'selected':''}>已录用</option>
                            <option value="rejected" ${a.status==='rejected'?'selected':''}>未通过</option>
                        </select>
                        ${a.status !== 'interviewed' ? `<button onclick="openInterviewModal(${a.id})" class="btn mt-sm" style="padding:4px 12px;background:#7c3aed;color:#fff;font-size:12px;">+ 安排面试</button>` : ''}
                        ${a.interview && a.interview.status === 'accepted' ? `<button onclick="openOfferModal(${a.id})" class="btn mt-sm" style="padding:4px 12px;background:#059669;color:#fff;font-size:12px;">发录用</button>` : ''}
                        ` : `<span style="font-size:12px;color:#94a3b8;">已结束</span>`}
                    </div>
                </div>
            </div>
        `}).join('');
    } catch(e) {
        document.getElementById('appList').innerHTML = '<div class="card empty-state-card text-danger">加载失败</div>';
    }
}

async function updateStatus(applicationId, status) {
    if (!status) return;
    if (!confirm('确认将状态改为「' + STATUS_MAP[status] + '」？')) {
        load(); return;
    }
    try {
        const r = await fetch('/api/my/jobs/' + jobId + '/applications/' + applicationId + '/status', {
            method: 'PUT',
            headers: {'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]').content},
            body: JSON.stringify({status: status})
        });
        const d = await r.json();
        if (d.code === 200) load(); else { alert(d.message); load(); }
    } catch(e) { alert('网络错误'); load(); }
}

function openInterviewModal(appId) {
    document.getElementById('ivAppId').value = appId;
    document.getElementById('interviewModal').style.display = 'flex';
}

function closeInterviewModal() {
    document.getElementById('interviewModal').style.display = 'none';
    document.getElementById('interviewForm').reset();
}

async function doScheduleInterview(e) {
    e.preventDefault();
    const appId = document.getElementById('ivAppId').value;
    const fd = new FormData(e.target);
    try {
        const r = await fetch('/api/my/jobs/' + jobId + '/applications/' + appId + '/interview', {
            method:'POST', body:fd,
            headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest','X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]').content}
        });
        const d = await r.json();
        if (d.code === 200) {
            closeInterviewModal();
            load();
        } else {
            alert(d.message || '操作失败');
        }
    } catch(e) { alert('网络错误'); }
}

function openOfferModal(appId) {
    document.getElementById('ofAppId').value = appId;
    document.getElementById('offerModal').style.display = 'flex';
}

function closeOfferModal() {
    document.getElementById('offerModal').style.display = 'none';
    document.getElementById('offerForm').reset();
}

async function doSendOffer(e) {
    e.preventDefault();
    const appId = document.getElementById('ofAppId').value;
    const fd = new FormData(e.target);
    try {
        const r = await fetch('/api/my/jobs/' + jobId + '/applications/' + appId + '/offer', {
            method:'POST', body:fd,
            headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest','X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]').content}
        });
        const d = await r.json();
        if (d.code === 200) {
            closeOfferModal();
            load();
        } else {
            alert(d.message || '操作失败');
        }
    } catch(e) { alert('网络错误'); }
}

load();
</script>
@endsection
