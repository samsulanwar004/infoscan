<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use App\Services\CategoryService;

class ProductCategoryController extends AdminController
{
    /**
     * @var string
     */
    protected $redirectAfterSave = 'product-categories';

    /**
     * Index Product Categories.
     *
     * @return mixed
     */
    public function index()
    {
        $this->isAllowed('ProductCategories.List');
        $categories = (new CategoryService)->getAllCategories();

        return view('categories.index', compact('categories'));
    }

    /**
     * Show create category.
     *
     * @return mixed
     */
    public function create()
    {
        $this->isAllowed('ProductCategories.Create');        
        return view('categories.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:225',
            'icon' => 'mimes:jpg,jpeg,png',
            'background' => 'mimes:jpg,jpeg,png',
        ]);

        try {
            (new CategoryService)->createCategory($request);
        } catch (\Exception $e) {
            return back()->with('errors', $e->getMessage());
        }

        return redirect($this->redirectAfterSave)->with('success', 'Product Category successfully created!');
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
     * Show edit category.
     *
     * @param  int  $id
     * @return mixed
     */
    public function edit($id)
    {
        $this->isAllowed('ProductCategories.Update');

        $category = (new CategoryService)->getCategoryById($id);

        return view('categories.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return bool
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required|max:225',
            'icon' => 'mimes:jpg,jpeg,png',
            'background' => 'mimes:jpg,jpeg,png',
        ]);

        try {
            (new CategoryService)->updateCategory($request, $id);
        } catch (\Exception $e) {
            return back()->with('errors', $e->getMessage());
        }

        return redirect($this->redirectAfterSave)->with('success', 'Product Category successfully updated!');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return bool
     */
    public function destroy($id)
    {
        try {
            $c = (new CategoryService)->getCategoryById($id);
            $c->delete();

        } catch (\Exception $e) {
            return back()->with('errors'. $e->getMessage());
        }

        return redirect($this->redirectAfterSave)->with('success', 'Product Category successfully deleted!');
    }
}
