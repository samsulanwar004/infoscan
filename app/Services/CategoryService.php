<?php

namespace App\Services;

use App\ProductCategory;

class CategoryService
{

	public function getAllCategories()
	{
		return ProductCategory::all();
	}

	public function getCategoryById($id)
	{
		return ProductCategory::where('id', $id)->first();
	}

	public function createCategory($request)
	{
		$randomName = strtolower(str_random(10));
		$category = new ProductCategory;
		$category->name = $request->input('name');

		if ($request->hasFile('icon')) {
            $icon = $request->file('icon');
            $iconname = sprintf(
                "%s-%s-%s.%s",
                'icon',
                $randomName,
                date('Ymdhis'),
                $icon->getClientOriginalExtension()
            );

            $category->icon = $iconname;
            $path = storage_path('app/public')."/product-categories/".$iconname;
            $resize = \Image::make($icon)->resize(200, 200);
            $resize->save($path);
        }

        if ($request->hasFile('background')) {
            $background = $request->file('background');
            $backgroundname = sprintf(
                "%s-%s-%s.%s",
                'background',
                $randomName,
                date('Ymdhis'),
                $background->getClientOriginalExtension()
            );

            $category->background = $backgroundname;
            $path = storage_path('app/public')."/product-categories/".$backgroundname;
            $resize = \Image::make($background)->resize(400, 150);
            $resize->save($path);
        }

		$category->save();

		return $category;
	}

	public function updateCategory($request, $id)
	{
		$randomName = strtolower(str_random(10));
		$category = $this->getCategoryById($id);
		$category->name = $request->input('name');

		if ($request->hasFile('icon') != null && $category->icon == true) {
            \Storage::delete('public/product-categories/' . $category->icon);
        }

        if ($request->hasFile('background') != null && $category->background == true) {
            \Storage::delete('public/product-categories/' . $category->background);
        }

        if ($request->hasFile('icon')) {
            $icon = $request->file('icon');
            $iconname = sprintf(
                "%s-%s-%s.%s",
                'icon',
                $randomName,
                date('Ymdhis'),
                $icon->getClientOriginalExtension()
            );

            $category->icon = $iconname;
            $path = storage_path('app/public')."/product-categories/".$iconname;
            $resize = \Image::make($icon)->resize(200, 200);
            $resize->save($path);
        }

        if ($request->hasFile('background')) {
            $background = $request->file('background');
            $backgroundname = sprintf(
                "%s-%s-%s.%s",
                'background',
                $randomName,
                date('Ymdhis'),
                $background->getClientOriginalExtension()
            );

            $category->background = $backgroundname;
            $path = storage_path('app/public')."/product-categories/".$backgroundname;
            $resize = \Image::make($background)->resize(400, 150);
            $resize->save($path);
        }

        $category->update();

        return $category;
	}
}