<?php


namespace App\Http\Controllers\Web;

use App\Services\PointService;
use Illuminate\Http\Request;

class PointController extends AdminController
{
    public function index(Request $request)
    {
        if($request->wantsJson()) {
            return (new PointService)->getPivotGrid();
        }

        return view('points.index');
    }

    public function show($id)
    {

    }

    public function create()
    {

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
}
