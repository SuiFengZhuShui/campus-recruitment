<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Enterprise;
use Illuminate\Http\Request;

class EnterpriseController extends Controller
{
    public function index(Request $request)
    {
        $query = Enterprise::with('college:id,name')
            ->where('status', 'approved')
            ->orderBy('created_at', 'desc');

        if ($request->keyword) {
            $keyword = str_replace(['%', '_'], ['\%', '\_'], $request->keyword);
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                  ->orWhere('industry', 'like', "%{$keyword}%");
            });
        }
        if ($request->industry) {
            $query->where('industry', $request->industry);
        }

        $enterprises = $query->paginate(15);

        return response()->json([
            'code' => 200,
            'message' => 'success',
            'data' => [
                'list' => $enterprises->items(),
                'meta' => [
                    'current_page' => $enterprises->currentPage(),
                    'per_page' => $enterprises->perPage(),
                    'total' => $enterprises->total(),
                    'last_page' => $enterprises->lastPage(),
                ],
            ],
        ]);
    }

    public function show($id)
    {
        $enterprise = Enterprise::with(['college:id,name', 'jobs' => function ($q) {
            $q->where('status', 'active')->orderBy('created_at', 'desc')->take(10);
        }])->where('status', 'approved')->findOrFail($id);

        return response()->json([
            'code' => 200,
            'message' => 'success',
            'data' => $enterprise,
        ]);
    }

    public function industries()
    {
        $industries = Enterprise::where('status', 'approved')->distinct()->pluck('industry');

        return response()->json(['code' => 200, 'message' => 'success', 'data' => $industries]);
    }
}
