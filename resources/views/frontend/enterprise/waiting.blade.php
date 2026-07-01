@extends('frontend.layout')

@section('title', '审核中')

@section('content')
<div class="container" style="max-width:500px;margin-top:80px;">
    <div class="card" style="text-align:center;padding:48px 32px;">
        <div style="font-size:48px;margin-bottom:16px;">📋</div>
        <h2 style="font-size:20px;margin-bottom:8px;color:#1e293b;">注册成功，等待审核</h2>
        <p style="font-size:14px;color:#64748b;line-height:1.8;">
            您的企业资料已提交，学校管理员正在审核。<br>
            审核通过后即可发布岗位、查看投递。
        </p>
        <div class="mt-xl" style="padding:16px;background:#fef3c7;border-radius:8px;font-size:13px;color:#92400e;">
            提示：请确保上传的营业执照、身份证和授权书清晰有效，审核通常需要1-2个工作日。
        </div>
        <div class="mt-xl">
            <a href="/enterprise/docs/upload" class="btn btn-primary" style="padding:10px 24px;">上传企业资质</a>
        </div>
        <a href="/" class="btn-back mt-lg" onclick="return goBack('/')">&larr; 返回首页</a>
    </div>
</div>
@endsection
