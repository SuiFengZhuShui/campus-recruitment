<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\School;
use Illuminate\Http\Request;

class SchoolController extends Controller
{
    public function index()
    {
        $schools = School::withCount('colleges')->orderBy('name')->paginate(15);

        return view('admin.schools.index', compact('schools'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100|unique:schools,name',
        ]);

        School::create($data);

        return back()->with('success', '已添加学校「' . $data['name'] . '」');
    }

    public function update(Request $request, $id)
    {
        $school = School::findOrFail($id);

        $data = $request->validate([
            'name' => 'string|max:100|unique:schools,name,' . $id,
        ]);

        $school->fill($data)->save();

        return back()->with('success', '已更新学校「' . $school->name . '」');
    }

    public function destroy($id)
    {
        $school = School::findOrFail($id);
        $name = $school->name;
        $school->delete();

        return back()->with('success', '已删除学校「' . $name . '」');
    }
}
