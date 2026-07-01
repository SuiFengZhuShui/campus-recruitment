@extends('layouts.admin')

@section('title', '学院管理')

@section('content')
<div class="page-header">
    <h1>学院管理</h1>
</div>

@if (session('success'))
    <div class="flash flash-success">{{ session('success') }}</div>
@endif

<div style="display:grid;grid-template-columns:350px 1fr;gap:24px;">
    <!-- Add form -->
    <div class="sidebar-form">
        <h3 class="section-title">添加学院</h3>
        <form method="POST" action="{{ route('admin.colleges.store') }}">
            @csrf
            <div class="form-group">
                <label class="form-label">学院名称</label>
                <input type="text" name="name" required maxlength="100" class="form-input" placeholder="例：计算机科学与技术学院">
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
                    <th>学院名称</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($colleges as $college)
                <tr>
                    <td>{{ $college->id }}</td>
                    <td>{{ $college->name }}</td>
                    <td>
                        <div class="flex gap-xs" style="flex-shrink:0;white-space:nowrap;">
                            <a href="#" onclick="event.preventDefault();editCollege({{ $college->id }}, '{{ $college->name }}');" class="btn btn-primary btn-xs">编辑</a>
                            <form method="POST" action="{{ url('admin/colleges/' . $college->id) }}" style="display:flex;margin:0;padding:0;" onsubmit="return confirm('确定删除？');">@csrf @method('DELETE')<button type="submit" class="btn btn-danger btn-xs">删除</button></form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="pagination-container">{{ $colleges->links() }}</div>
    </div>
</div>

<!-- Edit modal -->
<div id="editModal" class="modal-overlay">
    <div class="modal-box">
        <h3 class="modal-title">编辑学院</h3>
        <form id="editForm" method="POST">
            @csrf @method('PUT')
            <div class="form-group">
                <label class="form-label">学院名称</label>
                <input type="text" name="name" id="editName" required maxlength="100" class="form-input">
            </div>
            <div class="flex gap-sm">
                <button type="submit" class="btn btn-primary" style="flex:1;">保存</button>
                <button type="button" onclick="document.getElementById('editModal').style.display='none'" class="btn" style="flex:1;background:#e2e8f0;">取消</button>
            </div>
        </form>
    </div>
</div>

<script>
function editCollege(id, name) {
    document.getElementById('editForm').action = '/admin/colleges/' + id;
    document.getElementById('editName').value = name;
    document.getElementById('editModal').style.display = 'flex';
}
</script>
@endsection
