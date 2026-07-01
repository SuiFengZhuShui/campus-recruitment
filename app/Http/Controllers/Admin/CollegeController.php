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
        $colleges = College::orderBy('name')->paginate(15);

        return view('admin.colleges.index', compact('colleges'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
        ]);

        // Auto-set to the only school
        $school = School::first();
        $data['school_id'] = $school ? $school->id : null;

        College::create($data);

        return back()->with('success', '已添加学院「' . $data['name'] . '」');
    }

    public function update(Request $request, $id)
    {
        $college = College::findOrFail($id);

        $data = $request->validate([
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
