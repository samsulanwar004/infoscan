<?php

namespace App\Http\Controllers\Web;

use App\MerchantUser;
use Illuminate\Http\Request;
use App\Services\PromotionService;
use Auth;

class PromotionController extends AdminController
{

    /**
     * @var string
     */
    protected $redirectAfterSave = 'promotions';

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    { 
        if (empty($mi = $this->getMerchantIdByAuth())){
            $promos = (new PromotionService)->getAllPromotion();
        } else {
            $promos = (new PromotionService)->getPromotionByMerchantId($mi);
        }
        return view('promotions.index', compact('promos'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view('promotions.create');
    }

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required|max:255',
            'description' => 'required|max:255'
        ]);

        try {
            // Found merchant
            $mi = $this->getMerchantIdByAuth();

            (new PromotionService)->persistPromotion($request, $mi);
        } catch (\Exception $e) {
            return back()->with('errors', $e->getMessage());
        }

        return redirect($this->redirectAfterSave)->with('success', 'Promotion successfully created!');
    }

    /**
     * @param $id
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        $promotion = (new PromotionService)->getPromotionById($id);

        return view('promotions.edit', compact('promotion'));
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param null|int $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id = null)
    {
        $this->validate($request, [
            'title' => 'required|max:255',
            'description' => 'required|max:255'
        ]);

        try {
            // Found merchant
            $mi = $this->getMerchantIdByAuth();

            (new PromotionService)->persistPromotion($request, $mi, $id);
        } catch (\Exception $e) {
            return back()->with('errors', $e->getMessage());
        }

        return redirect($this->redirectAfterSave)->with('success', 'Promotion successfully updated!');
    }

    public function destroy($id)
    {
        try {
            $p = (new PromotionService)->getPromotionById($id);
            $p->delete();
        } catch (\Exception $e) {
            return back()->with('errors'. $e->getMessage());
        }

        return redirect($this->redirectAfterSave)->with('success', 'Promotion successfully deleted!');
    }

    private function getMerchantIdByAuth()
    {
        $mu = MerchantUser::where('user_id', '=', Auth::user()->id)->first();
        return isset($mu->merchant_id) ? $mu->merchant_id : null;
    }
}
