@extends('frontend.layout')

@section('title', '我的简历')

@section('content')
<div class="container" style="margin-top:24px;max-width:600px;">
    <a href="/student/dashboard" class="btn-back mb-lg" onclick="return goBack('/student/dashboard')">&larr; 返回个人中心</a>
    <h1 class="text-3xl mb-lg" style="font-weight:700;">我的简历</h1>

    <div class="card" style="padding:24px;">
        <div id="resumeInfo" class="mb-lg"></div>

        <form id="uploadForm" onsubmit="uploadResume(event)">
            @csrf
            <div class="form-group">
                <label class="form-label">上传简历文件</label>
                <input type="file" name="resume" id="resumeFile" accept=".pdf,.doc,.docx" required class="text-sm" style="width:100%;">
                <div class="text-xs text-light mt-xs">支持 PDF、DOC、DOCX 格式，最大 10MB</div>
            </div>
            <button type="submit" class="btn btn-primary w-full mt-md" id="uploadBtn" style="padding:12px;font-size:16px;justify-content:center;">上传简历</button>
        </form>

        <div id="uploadMsg" class="mt-md"></div>
    </div>
</div>

<script>
async function loadResume() {
    try {
        const r = await fetch('/api/my/profile', {headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest'}});
        if (r.status === 401) { location.href = '/login'; return; }
        const d = await r.json();
        if (d.code === 200 && d.data) {
            const s = d.data;
            if (s.resume_path) {
                document.getElementById('resumeInfo').innerHTML = `
                    <div style="background:#ecfdf5;border:1px solid #6ee7b7;border-radius:8px;padding:16px;">
                        <div class="text-base" style="color:#065f46;font-weight:500;">✅ 已上传简历</div>
                        <a href="/api/my/resume" target="_blank" class="btn btn-sm mt-sm" style="display:inline-block;">预览简历</a>
                    </div>`;
            } else {
                document.getElementById('resumeInfo').innerHTML = `
                    <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:8px;padding:16px;">
                        <div class="text-base" style="color:#991b1b;">未上传简历</div>
                    </div>`;
            }
        }
    } catch(e) {}
}

async function uploadResume(e) {
    e.preventDefault();
    const file = document.getElementById('resumeFile').files[0];
    if (!file) return;

    const btn = document.getElementById('uploadBtn');
    btn.disabled = true;
    btn.textContent = '上传中...';

    const fd = new FormData();
    fd.append('resume', file);

    try {
        const r = await fetch('/api/my/resume', {
            method: 'POST',
            body: fd,
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
            }
        });
        const d = await r.json();
        const msg = document.getElementById('uploadMsg');
        if (d.code === 200) {
            msg.innerHTML = '<div class="text-success-dark text-center">✅ ' + d.message + '</div>';
            loadResume();
        } else {
            msg.innerHTML = '<div class="text-danger text-center">❌ ' + (d.message || '上传失败') + '</div>';
        }
    } catch(e) {
        document.getElementById('uploadMsg').innerHTML = '<div class="text-danger text-center">网络错误</div>';
    }
    btn.disabled = false;
    btn.textContent = '上传简历';
}

loadResume();
</script>
@endsection
