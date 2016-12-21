<?php

namespace App\Http\Controllers\Web;

use App\Services\PointService;
use Illuminate\Http\Request;

class PointController extends AdminController
{
    public function index(Request $request)
    {
        $this->isAllowed('Point.List');
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

        return view('points.edit', compact($categories));
    }

    public function store(Request $request)
    {

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
}
