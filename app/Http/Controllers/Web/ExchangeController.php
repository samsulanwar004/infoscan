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
        $rates = Exchange::orderBy('created_at', 'DESC')->get();

        return view('exchange.index', compact('rates'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view('exchange.create');
    }

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        try {
            $this->persistExchange($request);
        } catch (\Exception $e) {
            return back()->with('errors', $e->getMessage());
        }

        return redirect($this->redirectAfterSave)->with('success', 'Exchange Rate Successfully Created!');
    }

    /**
     * @param $id
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        $rate = $this->getRateById($id);

        return view('exchange.edit', compact($rate));
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param null $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id = null)
    {
        try {
            $this->persistExchange($request, $id);
        } catch (\Exception $e) {
            return back()->with('errors', $e->getMessage());
        }

        return redirect($this->redirectAfterSave)->with('success', 'Exchange Rate Successfully Updated!');
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
        $r->cash_per_unit = $request->input('cash_per_unit');
        $r->point_unit_count = $request->input('point_unit_count');

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
