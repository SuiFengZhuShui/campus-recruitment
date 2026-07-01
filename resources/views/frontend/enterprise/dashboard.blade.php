@extends('frontend.layout')

@section('title', '企业首页')

@section('content')
<div class="container" style="margin-top:24px;">
    <h1 class="text-3xl mb-lg" style="font-weight:700;">企业后台</h1>

    <div class="dash-stats">
        <a href="/enterprise/jobs" class="stat-card card-hover">
            <div class="stat-label">在招岗位</div>
            <div class="stat-value" id="jobCount">--</div>
        </a>
        <div class="stat-card">
            <div class="stat-label">总投递数</div>
            <div class="stat-value text-primary" id="applyCount">--</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">面试中</div>
            <div class="stat-value text-warning" id="interviewCount">--</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">已录用</div>
            <div class="stat-value text-success-dark" id="offerCount">--</div>
        </div>
    </div>

    <div class="dash-bottom">
        <div class="card">
            <h3 class="section-title">快捷入口</h3>
            <div class="dash-quick-links">
                <a href="/enterprise/jobs">→ 岗位管理</a>
                <a href="/enterprise/resumes">→ 浏览学生简历</a>
                <a href="/enterprise/docs/upload">→ 上传企业资质</a>
                <a href="/enterprise/profile">→ 企业信息</a>
            </div>
        </div>
    </div>
</div>

<script>
async function loadDashboard() {
    try {
        const [entR, jobsR] = await Promise.all([
            fetch('/api/my/enterprise', {headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest'}}),
            fetch('/api/my/jobs', {headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest'}})
        ]);

        if (entR.status === 401) { location.href = '/login'; return; }

        const ent = await entR.json();
        if (ent.code === 403) { location.href = '/enterprise/waiting'; return; }

        if (jobsR.ok) {
            const jobs = await jobsR.json();
            if (jobs.code === 200) {
                document.getElementById('jobCount').textContent = (jobs.data?.list || []).filter(j => j.status === 'active').length;
            }
        }

        // Count applications across all jobs
        if (jobsR.ok) {
            const jobsData = await jobsR.json();
            if (jobsData.code === 200) {
                let totalApps = 0;
                const jobs = jobsData.data?.list || [];
                for (const job of jobs) {
                    try {
                        const appR = await fetch('/api/my/jobs/' + job.id + '/applications?per_page=1', {headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest'}});
                        const appD = await appR.json();
                        if (appD.code === 200) {
                            totalApps += appD.data?.meta?.total || 0;
                        }
                    } catch(e) {}
                }
                document.getElementById('applyCount').textContent = totalApps;
            }
        }
    } catch(e) {}
}

loadDashboard();
</script>
@endsection
