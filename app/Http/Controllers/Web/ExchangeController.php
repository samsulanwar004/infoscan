<?php

namespace App\Http\Controllers\Web;

use App\Exchange;
use App\CityRate;
use App\Services\LocationService;
use Illuminate\Http\Request;

class ExchangeController extends AdminController
{
    /**
     * @var string
     */
    protected $redirectAfterSave = 'exchange';

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $this->isAllowed('Exchange.List');
        $rates = Exchange::orderBy('created_at', 'DESC')->paginate(50);
        $citys = CityRate::orderBy('created_at', 'DESC')->paginate(50);

        return view('exchange.index', compact('rates', 'citys'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $this->isAllowed('Exchange.Create');
        return view('exchange.create');
    }

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'cash' => 'required|numeric',
            // 'cash' => 'required|numeric|different:point',
            // 'point' => 'required|numeric',
            'minimum_point' => 'required|numeric',
        ]);

        try {
            // if ($request->input('cash') > $request->input('point')) {
            //     throw new \Exception("Point must be greater than Cash", 1);                
            // }
            $this->persistExchange($request);
        } catch (\Exception $e) {
            return back()->with('errors', $e->getMessage());
        }

        return redirect($this->redirectAfterSave)->with('success', 'Exchange Rate successfully created!');
    }

    /**
     * @param $id
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        $this->isAllowed('Exchange.Update');
        $rate = $this->getRateById($id);

        return view('exchange.edit', compact('rate'));
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param null $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id = null)
    {
        $this->validate($request, [
            'cash' => 'required|numeric',
            // 'cash' => 'required|numeric|different:point',
            // 'point' => 'required|numeric',
            'minimum_point' => 'required|numeric',
        ]);

        try {
            // if ($request->input('cash') > $request->input('point')) {
            //     throw new \Exception("Point must be greater than Cash", 1);                
            // }
            $this->persistExchange($request, $id);
        } catch (\Exception $e) {
            return back()->with('errors', $e->getMessage());
        }

        return redirect($this->redirectAfterSave)->with('success', 'Exchange Rate successfully updated!');
    }

    public function destroy($id)
    {
        try {
            $exchange = $this->getRateById($id);
            $exchange->delete();
        } catch (\Exception $e) {
            return back()->with('errors', $e->getMessage());
        }

        return redirect($this->redirectAfterSave)->with('success', 'Exchange Rate successfully deleted!');
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param null|int $id
     *
     * @return mixed|bool
     */
    private function persistExchange(Request $request, $id = null)
    {
        $r = is_null($id) ? new Exchange : $this->getRateById($id);
        $r->cash_per_unit = $request->input('cash');
        $r->point_unit_count = 1;
        // $r->point_unit_count = $request->input('point');
        $r->minimum_point = $request->input('minimum_point');

        return $r->save();
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    private function getRateById($id)
    {
        return Exchange::where('id', '=', $id)->first();
    }

    public function cityCreate()
    {
        $this->isAllowed('Exchange.Create');
        $provincies = (new LocationService)->getAllProvince();

        return view('exchange.city_create', compact('provincies'));
    }

    public function cityStore(Request $request)
    {
        $this->validate($request, [
            'city_name' => 'required',
            'cash' => 'required|numeric',
        ]);

        try {
            $this->persistCityRate($request);
        } catch (\Exception $e) {
            return back()->with('errors', $e->getMessage());
        }

        return redirect($this->redirectAfterSave)->with('success', 'City Rate successfully created!');
    }

    public function cityEdit($id)
    {
        $rate = $this->getCityRateById($id);
        $provincies = (new LocationService)->getAllProvince();

        return view('exchange.city_edit', compact('rate', 'provincies'));
    }

    public function cityUpdate(Request $request, $id)
    {
        $this->validate($request, [
            'city_name' => 'required',
            'cash' => 'required|numeric',
        ]);

        try {
            $this->persistCityRate($request, $id);
        } catch (\Exception $e) {
            return back()->with('errors', $e->getMessage());
        }

        return redirect($this->redirectAfterSave)->with('success', 'City Rate successfully updated!');
    }

    public function cityDestroy($id)
    {
        try {
            $city = $this->getCityRateById($id);
            $city->delete();
        } catch (\Exception $e) {
            return back()->with('errors', $e->getMessage());
        }

        return redirect($this->redirectAfterSave)->with('success', 'City Rate successfully deleted!');
    }

    private function persistCityRate(Request $request, $id = null)
    {
        $r = is_null($id) ? new CityRate : $this->getCityRateById($id);
        $r->city_name = $request->input('city_name');
        $r->cash_per_unit = $request->input('cash');
        $r->is_active = $request->has('is_active') ? 1 : 0;

        return $r->save();
    }

    private function getCityRateById($id)
    {
        return CityRate::where('id', '=', $id)->first();
    }
}
