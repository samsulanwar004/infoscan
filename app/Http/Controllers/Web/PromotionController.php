<?php

namespace App\Http\Controllers\Web;

use App\Promotion;
use Illuminate\Http\Request;
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
        $this->validate($request, [
            'title' => 'required|max:255',
            'description' => 'required|max:255'
        ]);

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
        $this->validate($request, [
            'title' => 'required|max:255',
            'description' => 'required|max:255'
        ]);

        try {
            $this->persistPromotion($request, $id);
        } catch (\Exception $e) {
            return back()->with('errors', $e->getMessage());
        }

        return redirect($this->redirectAfterSave)->with('success', 'Promotion successfully updated!');
    }

    public function destroy($id)
    {
        try {
            $p = $this->getPromotionById($id);
            $p->delete();
        } catch (\Exception $e) {
            return back()->with('errors'. $e->getMessage());
        }

        return redirect($this->redirectAfterSave)->with('success', 'Promotion successfully deleted!');
    }

    /**
     * @param $request
     * @param null $id
     *
     * @return bool
     */
    public function persistPromotion($request, $id = null)
    {
        $date = $request->input('start_at');
        $dateArray = explode(' - ', $date);
        $p = is_null($id) ? new Promotion : $this->getPromotionById($id);
        $p->title = $request->input('title');
        $p->description = $request->input('description');
        $p->start_at = $dateArray[0];
        $p->end_at = $dateArray[1];
        $p->url = $request->input('url');
        $p->created_by = Auth::user()->id;
        $p->updated_by = isset($id) ? Auth::user()->id : null;
        $p->is_active = $request->has('is_active') ? 1 : 0;

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
