@extends('layouts.admin')

@section('title', '学号规则')

@section('content')
<div class="page-header">
    <h1>学号前缀匹配规则</h1>
    <p style="color:#64748b;font-size:13px;margin-top:4px;">根据学号前缀自动匹配学生所属学院</p>
</div>

@if (session('success'))
    <div style="background:#f0fdf4;color:#166534;padding:12px 16px;border-radius:8px;margin-bottom:16px;font-size:14px;">{{ session('success') }}</div>
@endif

<div style="display:grid;grid-template-columns:350px 1fr;gap:24px;">
    <!-- Add form -->
    <div style="background:#fff;padding:24px;border-radius:10px;box-shadow:0 1px 3px rgba(0,0,0,.06);align-self:start;">
        <h3 style="font-size:15px;margin-bottom:16px;color:#1e293b;">添加规则</h3>
        <form method="POST" action="{{ route('admin.rules.store') }}">
            @csrf
            <div style="margin-bottom:12px;">
                <label style="display:block;font-size:13px;color:#475569;margin-bottom:4px;">学校</label>
                <select name="school_id" id="addSchoolId" required style="width:100%;padding:8px 12px;border:1px solid #cbd5e1;border-radius:6px;font-size:14px;" onchange="filterColleges()">
                    <option value="">请选择</option>
                    @foreach ($schools as $school)
                        <option value="{{ $school->id }}">{{ $school->name }}</option>
                    @endforeach
                </select>
            </div>
            <div style="margin-bottom:12px;">
                <label style="display:block;font-size:13px;color:#475569;margin-bottom:4px;">学号前缀</label>
                <input type="text" name="prefix" required maxlength="20" style="width:100%;padding:8px 12px;border:1px solid #cbd5e1;border-radius:6px;font-size:14px;" placeholder="例：2024 或 202401">
            </div>
            <div style="margin-bottom:16px;">
                <label style="display:block;font-size:13px;color:#475569;margin-bottom:4px;">匹配学院</label>
                <select name="college_id" id="addCollegeId" required style="width:100%;padding:8px 12px;border:1px solid #cbd5e1;border-radius:6px;font-size:14px;">
                    <option value="">请选择</option>
                    @foreach ($colleges as $college)
                        <option value="{{ $college->id }}" data-school="{{ $college->school_id }}">{{ $college->name }}（{{ $college->school->name ?? '' }}）</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" style="width:100%;padding:10px;background:#3b82f6;color:#fff;border:none;border-radius:6px;font-size:14px;cursor:pointer;">添加</button>
        </form>
    </div>

    <!-- List -->
    <div style="background:#fff;border-radius:10px;box-shadow:0 1px 3px rgba(0,0,0,.06);padding:20px;">
        <table style="width:100%;border-collapse:collapse;">
            <thead>
                <tr style="text-align:left;border-bottom:1px solid #e2e8f0;">
                    <th style="padding:10px 12px;font-size:13px;color:#475569;">ID</th>
                    <th style="padding:10px 12px;font-size:13px;color:#475569;">前缀</th>
                    <th style="padding:10px 12px;font-size:13px;color:#475569;">学校</th>
                    <th style="padding:10px 12px;font-size:13px;color:#475569;">匹配学院</th>
                    <th style="padding:10px 12px;font-size:13px;color:#475569;">操作</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($rules as $rule)
                <tr style="border-bottom:1px solid #f1f5f9;">
                    <td style="padding:10px 12px;font-size:14px;">{{ $rule->id }}</td>
                    <td style="padding:10px 12px;font-size:14px;font-weight:500;">{{ $rule->prefix }}</td>
                    <td style="padding:10px 12px;font-size:14px;color:#64748b;">{{ $rule->school->name ?? '-' }}</td>
                    <td style="padding:10px 12px;font-size:14px;">{{ $rule->college->name ?? '-' }}</td>
                    <td style="padding:10px 12px;">
                        <button onclick="editRule({{ $rule->id }}, '{{ $rule->prefix }}', {{ $rule->school_id }}, {{ $rule->college_id }})" style="color:#3b82f6;border:none;background:none;cursor:pointer;font-size:13px;margin-right:8px;">编辑</button>
                        <form method="POST" action="{{ url('admin/rules/' . $rule->id) }}" style="display:inline;" onsubmit="return confirm('确定删除？')">
                            @csrf @method('DELETE')
                            <button type="submit" style="color:#ef4444;border:none;background:none;cursor:pointer;font-size:13px;">删除</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div style="margin-top:16px;">{{ $rules->links() }}</div>
    </div>
</div>

<!-- Edit modal -->
<div id="editModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.3);justify-content:center;align-items:center;z-index:100;">
    <div style="background:#fff;padding:24px;border-radius:12px;width:380px;">
        <h3 style="margin-bottom:16px;">编辑规则</h3>
        <form id="editForm" method="POST">
            @csrf @method('PUT')
            <div style="margin-bottom:12px;">
                <label style="display:block;font-size:13px;color:#475569;margin-bottom:4px;">学校</label>
                <select name="school_id" id="editSchoolId" style="width:100%;padding:8px 12px;border:1px solid #cbd5e1;border-radius:6px;font-size:14px;">
                    @foreach ($schools as $school)
                        <option value="{{ $school->id }}">{{ $school->name }}</option>
                    @endforeach
                </select>
            </div>
            <div style="margin-bottom:12px;">
                <label style="display:block;font-size:13px;color:#475569;margin-bottom:4px;">学号前缀</label>
                <input type="text" name="prefix" id="editPrefix" required maxlength="20" style="width:100%;padding:8px 12px;border:1px solid #cbd5e1;border-radius:6px;font-size:14px;">
            </div>
            <div style="margin-bottom:16px;">
                <label style="display:block;font-size:13px;color:#475569;margin-bottom:4px;">匹配学院</label>
                <select name="college_id" id="editCollegeId" style="width:100%;padding:8px 12px;border:1px solid #cbd5e1;border-radius:6px;font-size:14px;">
                    @foreach ($colleges as $college)
                        <option value="{{ $college->id }}">{{ $college->name }}（{{ $college->school->name ?? '' }}）</option>
                    @endforeach
                </select>
            </div>
            <div style="display:flex;gap:8px;">
                <button type="submit" style="flex:1;padding:10px;background:#3b82f6;color:#fff;border:none;border-radius:6px;cursor:pointer;">保存</button>
                <button type="button" onclick="document.getElementById('editModal').style.display='none'" style="flex:1;padding:10px;background:#e2e8f0;border:none;border-radius:6px;cursor:pointer;">取消</button>
            </div>
        </form>
    </div>
</div>

<script>
function editRule(id, prefix, schoolId, collegeId) {
    document.getElementById('editForm').action = '/admin/rules/' + id;
    document.getElementById('editPrefix').value = prefix;
    document.getElementById('editSchoolId').value = schoolId;
    document.getElementById('editCollegeId').value = collegeId;
    document.getElementById('editModal').style.display = 'flex';
}
function filterColleges() {
    var schoolId = document.getElementById('addSchoolId').value;
    var opts = document.getElementById('addCollegeId').options;
    for (var i = 1; i < opts.length; i++) {
        var dataSchool = opts[i].getAttribute('data-school');
        opts[i].style.display = !schoolId || dataSchool === schoolId ? '' : 'none';
    }
}
</script>
@endsection
