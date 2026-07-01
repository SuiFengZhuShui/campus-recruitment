<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\College;
use App\Models\School;
use App\Models\StudentIdRule;
use Illuminate\Http\Request;

class StudentIdRuleController extends Controller
{
    public function index()
    {
        $rules = StudentIdRule::with('college')->orderBy('prefix')->paginate(15);
        $colleges = College::orderBy('name')->get();

        return view('admin.rules.index', compact('rules', 'colleges'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'prefix' => 'required|string|max:20|unique:student_id_rules,prefix',
            'college_id' => 'required|exists:colleges,id',
        ]);

        // Auto-set school_id to the only school
        $school = School::first();
        $data['school_id'] = $school ? $school->id : null;

        StudentIdRule::create($data);

        return back()->with('success', '已添加规则：前缀 ' . $data['prefix']);
    }

    public function update(Request $request, $id)
    {
        $rule = StudentIdRule::findOrFail($id);

        $data = $request->validate([
            'prefix' => 'string|max:20|unique:student_id_rules,prefix,' . $id,
            'college_id' => 'exists:colleges,id',
        ]);

        $rule->fill($data)->save();

        return back()->with('success', '已更新规则');
    }

    public function destroy($id)
    {
        $rule = StudentIdRule::findOrFail($id);
        $rule->delete();

        return back()->with('success', '已删除规则');
    }
}
