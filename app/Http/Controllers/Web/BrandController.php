<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;

class BrandController extends AdminController
{

    /**
     * @var string
     */
    protected $redirectAfterSave = 'brands';

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $brands = Brand::paginate();

        return view('brand.index', compact('brands'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view('brand.create');
    }

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        try {
            $this->persistBrand($request);
        } catch (\Exception $e) {
            return back()->with('errors', $e->getMessage());
        }

        return redirect($this->redirectAfterSave)->with('success', 'Brand successfully saved!');
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        try {
            $this->persistBrand($request, $id);
        } catch (\Exception $e) {
            return back()->with('errors', $e->getMessage());
        }

        return redirect($this->redirectAfterSave)->with('success', 'Brand successfully updated!');
    }

    /**
     * @param $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        try {
            $b = $this->getBrandById($id);
            $b->delete();
        } catch (\Exception $e) {
            return back()->with('errors', $e->getMessage());
        }

        return redirect($this->redirectAfterSave)->with('success', 'Brand successfully deleted!');
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    private function getBrandById($id)
    {
        return Brand::where('id', '=', $id)->first();
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param null $id
     *
     * @return mixed
     */
    private function persistBrand(Request $request, $id = null)
    {
        $b = is_null($id) ? new Brand : $this->getBrandById($id);
        $b->name = $request->input('name');
        $b->company = $request->input('company');

        return $b->save();
    }
}
