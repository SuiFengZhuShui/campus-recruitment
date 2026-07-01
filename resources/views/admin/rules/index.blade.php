@extends('layouts.admin')

@section('title', '学号规则')

@section('content')
<div class="page-header">
    <h1>学号前缀匹配规则</h1>
    <p class="text-muted text-sm mt-xs">根据学号前缀自动匹配学生所属学院</p>
</div>

@if (session('success'))
    <div class="flash flash-success">{{ session('success') }}</div>
@endif

<div style="display:grid;grid-template-columns:350px 1fr;gap:24px;">
    <!-- Add form -->
    <div class="sidebar-form">
        <h3 class="section-title">添加规则</h3>
        <form method="POST" action="{{ route('admin.rules.store') }}">
            @csrf
            <div class="form-group">
                <label class="form-label">学号前缀</label>
                <input type="text" name="prefix" required maxlength="20" class="form-input" placeholder="例：2024 或 202401">
            </div>
            <div class="form-group">
                <label class="form-label">匹配学院</label>
                <select name="college_id" required class="form-input">
                    <option value="">请选择</option>
                    @foreach ($colleges as $college)
                        <option value="{{ $college->id }}">{{ $college->name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-primary w-full">添加</button>
        </form>
    </div>

    <!-- List -->
    <div class="table-card">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>前缀</th>
                    <th>匹配学院</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($rules as $rule)
                <tr>
                    <td>{{ $rule->id }}</td>
                    <td class="font-medium">{{ $rule->prefix }}</td>
                    <td>{{ $rule->college->name ?? '-' }}</td>
                    <td>
                        <div class="flex gap-xs" style="flex-shrink:0;white-space:nowrap;">
                            <a href="#" onclick="event.preventDefault();editRule({{ $rule->id }}, '{{ $rule->prefix }}', {{ $rule->college_id }});" class="btn btn-primary btn-xs">编辑</a>
                            <form method="POST" action="{{ url('admin/rules/' . $rule->id) }}" style="display:flex;margin:0;padding:0;" onsubmit="return confirm('确定删除？');">@csrf @method('DELETE')<button type="submit" class="btn btn-danger btn-xs">删除</button></form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="pagination-container">{{ $rules->links() }}</div>
    </div>
</div>

<!-- Edit modal -->
<div id="editModal" class="modal-overlay">
    <div class="modal-box">
        <h3 class="modal-title">编辑规则</h3>
        <form id="editForm" method="POST">
            @csrf @method('PUT')
            <div class="form-group">
                <label class="form-label">学号前缀</label>
                <input type="text" name="prefix" id="editPrefix" required maxlength="20" class="form-input">
            </div>
            <div class="form-group">
                <label class="form-label">匹配学院</label>
                <select name="college_id" id="editCollegeId" class="form-input">
                    @foreach ($colleges as $college)
                        <option value="{{ $college->id }}">{{ $college->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-sm">
                <button type="submit" class="btn btn-primary" style="flex:1;">保存</button>
                <button type="button" onclick="document.getElementById('editModal').style.display='none'" class="btn" style="flex:1;background:#e2e8f0;">取消</button>
            </div>
        </form>
    </div>
</div>

<script>
function editRule(id, prefix, collegeId) {
    document.getElementById('editForm').action = '/admin/rules/' + id;
    document.getElementById('editPrefix').value = prefix;
    document.getElementById('editCollegeId').value = collegeId;
    document.getElementById('editModal').style.display = 'flex';
}
</script>
@endsection
