<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use App\Services\PromotionService;
use App\Services\MerchantService;
use App\Services\TransactionService;
use App\Services\CategoryService;


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
        $this->isAllowed('Promotion.List');
        $promotion = new PromotionService;
        if (empty($mi = (new MerchantService)->getMerchantIdByAuth())){
            $promos = $promotion->getAllPromotion();
        } else {
            $promos = $promotion->getPromotionByMerchantId($mi);
        }
        return view('promotions.index', compact('promos'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $this->isAllowed('Promotion.Create');
        $categories = (new CategoryService)->getAllCategories();

        return view('promotions.create', compact('categories'));
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
            'description' => 'max:255',
            'image' => 'mimes:jpg,jpeg,png',
        ]);

        try {
            // Found merchant
            $mi = (new MerchantService)->getMerchantIdByAuth();

            (new PromotionService)->createPromotion($request, $mi);
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
        $this->isAllowed('Promotion.Update');
        $promotion = (new PromotionService)->getPromotionById($id);

        $categories = (new CategoryService)->getAllCategories();

        return view('promotions.edit', compact('promotion', 'categories'));
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
            'description' => 'max:255',
            'image' => 'mimes:jpg,jpeg,png'
        ]);

        try {
            // Found merchant
            $mi = (new MerchantService)->getMerchantIdByAuth();

            (new PromotionService)->updatePromotion($request, $id, $mi);
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

            if ($p->image != null) {
                \Storage::delete('public/promotions/' . $p->image);
            }
        } catch (\Exception $e) {
            return back()->with('errors'. $e->getMessage());
        }

        return redirect($this->redirectAfterSave)->with('success', 'Promotion successfully deleted!');
    }

}
