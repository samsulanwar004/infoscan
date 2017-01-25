<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use App\Services\PointService;

class PromoPointController extends AdminController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->isAllowed('Points.List');
        if ($request->wantsJson()) {
            return (new PointService)->getPromoPivotGrid();
        }

        return view('promopoints.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
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

        return view('promopoints.create', compact('levels', 'lastLevel'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:promo_points,city_name|max:100',
            'point_city' => 'required',
            'levels.*' => 'required'
        ]);

        try {     

            (new PointService)->addPromoLevelPoint($request);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'status' => 'ok',
            'message' => 'Promo Level Points successfully created!',
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->isAllowed('Points.Update');
        $promo = (new PointService)->getPromoPointById($id);
        $levels = $promo->levels;

        return view('promopoints.edit', compact('promo', 'levels'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required|max:100',
            'point_city' => 'required',
            'levels.*' => 'required'
        ]);

        try {
            (new PointService)->updatePromoLevelPoint($request, $id);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'status' => 'ok',
            'message' => 'Promo Level Points successfully updated!',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    protected function getLevels()
    {
        return (new PointService)->getLevels();
    }

    public function getPromoLevelTable(Request $request)
    {
        $this->isAllowed('Points.List');
        if ($request->wantsJson()) {
            return (new PointService)->getPromoPivotGrid();
        }
        return view('promopoints.table_promo_level_points');
    }
}
