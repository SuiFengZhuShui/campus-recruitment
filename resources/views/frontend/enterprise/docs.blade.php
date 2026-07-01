@extends('frontend.layout')

@section('title', '企业资质')

@section('content')
<div class="container" style="margin-top:24px;max-width:700px;">
    <a href="/enterprise/dashboard" class="btn-back mb-lg" onclick="return goBack('/enterprise/dashboard')">&larr; 返回企业后台</a>
    <h1 class="text-3xl mb-lg" style="font-weight:700;">企业资质</h1>

    <div id="docsList" class="mb-xl"></div>

    <div class="card" style="padding:24px;">
        <h3 class="section-title">上传资质文件</h3>
        <form id="uploadForm" onsubmit="uploadDoc(event)">
            @csrf
            <div class="form-group">
                <label class="form-label">文件类型</label>
                <select name="type" id="docType" required class="form-input form-input-lg">
                    <option value="license">营业执照</option>
                    <option value="id_card">经办人身份证</option>
                    <option value="authorization">招聘授权书</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">选择文件（JPG/PNG/PDF，≤10MB）</label>
                <input type="file" name="file" id="docFile" required accept=".jpg,.jpeg,.png,.pdf" class="text-sm" style="width:100%;">
            </div>
            <button type="submit" class="btn btn-primary w-full mt-md" id="uploadBtn" style="padding:12px;font-size:16px;justify-content:center;">上传</button>
        </form>
        <div id="uploadMsg" class="mt-md"></div>
    </div>
</div>

<script>
const typeNames = { license: '营业执照', id_card: '身份证', authorization: '授权书' };
const statusNames = { pending: '待审', approved: '已通过', rejected: '不通过' };

async function loadDocs() {
    try {
        const r = await fetch('/api/my/enterprise', {headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest'}});
        if (r.status === 401) { location.href = '/login'; return; }
        const d = await r.json();
        if (d.code === 403) { location.href = '/enterprise/waiting'; return; }
        if (d.code !== 200) return;

        const docs = d.data.docs || [];
        const status = d.data.status;
        let html = '';

        if (!docs.length) {
            html = '<div class="card empty-state-card">未上传资质文件</div>';
        } else {
            docs.forEach(doc => {
                html += `
                <div class="card mb-sm" style="padding:16px;">
                    <div class="flex" style="justify-content:space-between;align-items:center;">
                        <div>
                            <strong>${typeNames[doc.type] || doc.type}</strong>
                            <span class="text-sm text-light ml-sm">(${doc.file_name})</span>
                        </div>
                        <div class="flex gap-sm" style="align-items:center;">
                            <span class="badge badge-sm ${doc.status === 'approved' ? 'badge-approved' : doc.status === 'rejected' ? 'badge-rejected' : 'badge-pending'}">${statusNames[doc.status] || doc.status}</span>
                            ${doc.reject_reason ? `<span class="text-xs text-danger">原因: ${doc.reject_reason}</span>` : ''}
                            <button onclick="deleteDoc(${doc.id})" class="btn btn-xs text-danger">删除</button>
                        </div>
                    </div>
                </div>`;
            });
        }

        if (status === 'rejected' && d.data.audit_remark) {
            html += `<div class="card mb-sm" style="border-left:4px solid #ef4444;background:#fef2f2;padding:16px;"><strong class="text-danger">审核驳回：</strong>${d.data.audit_remark}</div>`;
        }

        document.getElementById('docsList').innerHTML = html;
    } catch(e) {}
}

async function uploadDoc(e) {
    e.preventDefault();
    const file = document.getElementById('docFile').files[0];
    if (!file) return;

    const btn = document.getElementById('uploadBtn');
    btn.disabled = true;
    btn.textContent = '上传中...';

    const fd = new FormData();
    fd.append('type', document.getElementById('docType').value);
    fd.append('file', file);

    try {
        const r = await fetch('/api/my/docs', {
            method: 'POST', body: fd,
            headers: {'Accept':'application/json','X-Requested-With':'XMLHttpRequest','X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]').content}
        });
        const d = await r.json();
        const msg = document.getElementById('uploadMsg');
        if (d.code === 200) {
            msg.innerHTML = '<div class="text-success-dark text-center">✅ ' + d.message + '</div>';
            document.getElementById('docFile').value = '';
            loadDocs();
        } else {
            msg.innerHTML = '<div class="text-danger text-center">❌ ' + (d.message || '上传失败') + '</div>';
        }
    } catch(e) {
        document.getElementById('uploadMsg').innerHTML = '<div class="text-danger text-center">网络错误</div>';
    }
    btn.disabled = false;
    btn.textContent = '上传';
}

async function deleteDoc(id) {
    if (!confirm('确定删除此文件？')) return;
    try {
        await fetch('/api/my/docs/' + id, { method:'DELETE', headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest','X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]').content} });
        loadDocs();
    } catch(e) {}
}

loadDocs();
</script>
@endsection
