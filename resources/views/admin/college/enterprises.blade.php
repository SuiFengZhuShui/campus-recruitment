@extends('layouts.admin')

@section('title', '对接企业')

@section('content')
<a href="{{ url('admin/college') }}" style="display:inline-flex;align-items:center;gap:4px;padding:8px 18px;background:linear-gradient(135deg,#c7915c,#d4a574);color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:500;text-decoration:none;margin-bottom:16px;">&larr; 返回学院首页</a>

<h1 style="font-size:20px;font-weight:600;margin-bottom:20px;">对接企业列表</h1>

@if($enterprises->isEmpty())
    <div style="background:#fff;padding:60px;text-align:center;color:#94a3b8;border-radius:10px;">暂无对接企业</div>
@else
    <div style="background:#fff;border-radius:10px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,.06);">
        <table style="width:100%;border-collapse:collapse;font-size:14px;">
            <thead>
                <tr style="background:#f8fafc;text-align:left;">
                    <th style="padding:12px 16px;color:#64748b;font-weight:500;">企业名称</th>
                    <th style="padding:12px 16px;color:#64748b;font-weight:500;">行业</th>
                    <th style="padding:12px 16px;color:#64748b;font-weight:500;">规模</th>
                    <th style="padding:12px 16px;color:#64748b;font-weight:500;">联系人</th>
                    <th style="padding:12px 16px;color:#64748b;font-weight:500;">手机</th>
                    <th style="padding:12px 16px;color:#64748b;font-weight:500;">状态</th>
                </tr>
            </thead>
            <tbody>
                @foreach($enterprises as $enterprise)
                <tr style="border-top:1px solid #f1f5f9;">
                    <td style="padding:12px 16px;font-weight:500;">{{ $enterprise->name }}</td>
                    <td style="padding:12px 16px;">{{ $enterprise->industry }}</td>
                    <td style="padding:12px 16px;">{{ $enterprise->scale ?? '-' }}</td>
                    <td style="padding:12px 16px;">{{ $enterprise->contact_name }}</td>
                    <td style="padding:12px 16px;">{{ $enterprise->contact_phone }}</td>
                    <td style="padding:12px 16px;">
                        <span style="display:inline-block;padding:2px 10px;border-radius:12px;font-size:12px;background:{{ $enterprise->status === 'approved' ? '#d1fae5' : ($enterprise->status === 'rejected' ? '#fee2e2' : '#fef3c7') }};color:{{ $enterprise->status === 'approved' ? '#065f46' : ($enterprise->status === 'rejected' ? '#991b1b' : '#92400e') }};">{{ ['pending'=>'待审核','approved'=>'已通过','rejected'=>'已驳回'][$enterprise->status] }}</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div style="margin-top:16px;">{{ $enterprises->links() }}</div>
@endif
@endsection
