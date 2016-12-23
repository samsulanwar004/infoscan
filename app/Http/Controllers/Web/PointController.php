<?php

namespace App\Http\Controllers\Web;

use App\Services\PointService;
use Illuminate\Http\Request;

class PointController extends AdminController
{
    public function index(Request $request)
    {
        $this->isAllowed('Points.List');
        if ($request->wantsJson()) {
            return (new PointService)->getPivotGrid();
        }

        return view('points.index');
    }

    public function show($id)
    {

    }

    public function create()
    {
        $categories = $this->getSnapCategory();
        $levels = $this->getLevels();

        return view('points.create', compact('categories', 'levels'));
    }

    public function store(Request $request)
    {
        try {
            (new PointService)->addTaskLevelPoint($request);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'status' => 'ok',
            'message' => 'Task Level Point created!',
        ]);
    }

    public function edit($id)
    {

    }

    public function update(Request $request, $id)
    {

    }

    public function destroy($id)
    {

    }

    protected function getSnapCategory($key = null)
    {
        if($key) {
            return config("common.snap_category.$key");
        }

        return config('common.snap_category');
    }

    protected function getLevels()
    {
        return (new PointService)->getLevels();
    }
}
