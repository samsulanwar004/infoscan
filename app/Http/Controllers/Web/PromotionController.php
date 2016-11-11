<?php

namespace App\Http\Controllers\Web;

use App\Promotion;
use Illuminate\Http\Request;

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
        $promos = Promotion::where('is_active', '=', 1)->paginate(50);

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
        try {
            $this->persistPromotion($request);
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
        $promotion = $this->getPromotionById($id);

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
        try {
            $this->persistPromotion($request, $id);
        } catch (\Exception $e) {
            return back()->with('errors', $e->getMessage());
        }

        return redirect($this->redirectAfterSave)->with('success', 'Promotion successfully updated!');
    }

    /**
     * @param $request
     * @param null $id
     *
     * @return bool
     */
    public function persistPromotion($request, $id = null)
    {
        $p = is_null($id) ? new Promotion : $this->getPromotionById($id);
        $p->title = $request->input('title');
        $p->description = $request->input('description');
        $p->start_at = $request->input('start_at');
        $p->end_at = $request->input('end_at');
        $p->url = $request->input('url');
        $p->status = $request->input('status');

        return $p->save();
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    private function getPromotionById($id)
    {
        return Promotion::where('id', '=', $id)->first();
    }
}
