<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\College;
use App\Models\School;
use Illuminate\Http\Request;

class CollegeController extends Controller
{
    public function index()
    {
        $colleges = College::with('school')->orderBy('name')->paginate(15);
        $schools = School::orderBy('name')->get();

        return view('admin.colleges.index', compact('colleges', 'schools'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'school_id' => 'required|exists:schools,id',
            'name' => 'required|string|max:100',
        ]);

        College::create($data);

        return back()->with('success', '已添加学院「' . $data['name'] . '」');
    }

    public function update(Request $request, $id)
    {
        $college = College::findOrFail($id);

        $data = $request->validate([
            'school_id' => 'exists:schools,id',
            'name' => 'string|max:100',
        ]);

        $college->fill($data)->save();

        return back()->with('success', '已更新学院「' . $college->name . '」');
    }

    public function destroy($id)
    {
        $college = College::findOrFail($id);
        $name = $college->name;
        $college->delete();

        return back()->with('success', '已删除学院「' . $name . '」');
    }
}
