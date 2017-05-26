<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use App\Services\PointService;

class BonusPointController extends AdminController
{
    public function index(Request $request)
    {
    	$this->isAllowed('Points.List');
        if ($request->wantsJson()) {
            return (new PointService)->getBonusPivotGrid();
        }

        return view('bonuspoints.index');
    }

    public function create()
    {
    	$this->isAllowed('Points.Create');
        $levels = $this->getLevels();
        $lastLevels = (new PointService)->lastLevel();

        if ($lastLevels) {
            $arrayLevel = explode(' ', $lastLevels->name);
            $lastLevel = $arrayLevel[1];
        } else {
            $lastLevel = 0;
        }

        return view('bonuspoints.create', compact('levels', 'lastLevel'));
    }

    public function store(Request $request)
    {
    	$this->validate($request, [
            'bonus_name' => 'required|unique:bonus_points,bonus_name|max:100',
            'levels.*' => 'required'
        ]);

        try {     

            (new PointService)->addBonusLevelPoint($request);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'status' => 'ok',
            'message' => 'Bonus Level Points successfully created!',
        ]);
    }

    public function update(Request $request, $id)
    {
    	$this->validate($request, [
            'bonus_name' => 'required|max:100',
            'levels.*' => 'required'
        ]);

        try {
            (new PointService)->updateBonusLevelPoint($request, $id);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'status' => 'ok',
            'message' => 'Bonus Level Points successfully updated!',
        ]);
    }

    public function edit($id)
    {
    	$this->isAllowed('Points.Update');
        $bonus = (new PointService)->getBonusPointById($id);
        $levels = $bonus->levels;

        return view('bonuspoints.edit', compact('bonus', 'levels'));
    }

    protected function getLevels()
    {
        return (new PointService)->getLevels();
    }
}
