<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;

class BrandController extends AdminController
{

    protected $redirectAfterSave = 'brands';

    public function index()
    {
        $brands = Brand::paginate();
        return view('brand.index', compact('brands'));
    }

    public function create()
    {
        return view('brand.create');
    }

    public function store(Request $request)
    {
        try {
            $this->persistBrand($request);
        } catch (\Exception $e) {
            return back()->with('errors', $e->getMessage());
        }

        return redirect($this->redirectAfterSave)->with('success', 'Brand successfully saved!');
    }

    public function store(Request $request, $id)
    {
        try {
            $this->persistBrand($request, $id);
        } catch (\Exception $e) {
            return back()->with('errors', $e->getMessage());
        }

        return redirect($this->redirectAfterSave)->with('success', 'Brand successfully updated!');
    }

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

    private function getBrandById($id)
    {
        return Brand::where('id', '=', $id)->first();
    }

    private function persistBrand(Request $request, $id = null)
    {
        $b = is_null($id) ? new Brand : $this->getBrandById($id);
        $b->name = $request->input('name');
        $b->company = $request->input('company');
        return $sb->save();
    }
}
