@extends('layouts.admin')

@section('title', '对接企业')

@section('content')
<a href="{{ url('admin/college') }}" class="btn-back mb-lg">&larr; 返回学院首页</a>

<h1 class="page-title">{{ $isCollegeScoped ? '对接企业列表' : '全部企业列表' }}</h1>

@if($enterprises->isEmpty())
    <div class="card empty-state-card">暂无对接企业</div>
@else
    <div class="table-card" style="overflow:hidden;">
        <table class="table">
            <thead>
                <tr style="background:#f8fafc;">
                    <th>企业名称</th>
                    <th>行业</th>
                    <th>规模</th>
                    @unless($isCollegeScoped)
                    <th>所属学院</th>
                    @endunless
                    <th>联系人</th>
                    <th>手机</th>
                    <th>状态</th>
                </tr>
            </thead>
            <tbody>
                @foreach($enterprises as $enterprise)
                <tr>
                    <td class="font-medium">{{ $enterprise->name }}</td>
                    <td>{{ $enterprise->industry }}</td>
                    <td>{{ $enterprise->scale ?? '-' }}</td>
                    @unless($isCollegeScoped)
                    <td>{{ $enterprise->college->name ?? '-' }}</td>
                    @endunless
                    <td>{{ $enterprise->contact_name }}</td>
                    <td>{{ $enterprise->contact_phone }}</td>
                    <td>
                        <span class="badge
                            @if($enterprise->status === 'approved') badge-approved
                            @elseif($enterprise->status === 'rejected') badge-rejected
                            @else badge-pending
                            @endif
                        ">{{ ['pending'=>'待审核','approved'=>'已通过','rejected'=>'已驳回'][$enterprise->status] }}</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="pagination-container">{{ $enterprises->links() }}</div>
@endif
@endsection
