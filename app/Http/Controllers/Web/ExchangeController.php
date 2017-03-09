<?php

namespace App\Http\Controllers\Web;

use App\Exchange;
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

        return view('exchange.index', compact('rates'));
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
            'cash' => 'required|numeric|different:point',
            'point' => 'required|numeric',
            'minimum_point' => 'required|numeric',
        ]);

        try {
            if ($request->input('cash') > $request->input('point')) {
                throw new \Exception("Point must be greater than Cash", 1);                
            }
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
            'cash' => 'required|numeric|different:point',
            'point' => 'required|numeric',
            'minimum_point' => 'required|numeric',
        ]);

        try {
            if ($request->input('cash') > $request->input('point')) {
                throw new \Exception("Point must be greater than Cash", 1);                
            }
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
    public function persistExchange(Request $request, $id = null)
    {
        $r = is_null($id) ? new Exchange : $this->getRateById($id);
        $r->cash_per_unit = $request->input('cash');
        $r->point_unit_count = $request->input('point');
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
}
