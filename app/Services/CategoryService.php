<?php

namespace App\Services;

use App\ProductCategory;
use App\Libraries\ImageFile;
use Image;
use Storage;

class CategoryService
{

    const RESIZE_ICON = [200,null];
    const RESIZE_BACKGROUND = [400,null];
    const DEFAULT_FILE_DRIVER = 's3';

    public function __construct()
    {
        $this->s3Url = env('S3_URL', '');
    }

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

            //tmp file in storage local
            $path = storage_path('app/public')."/product-categories/".$iconname;
            //resize image
            $image = new ImageFile(Image::make($icon->path())
                ->resize(self::RESIZE_ICON[0],self::RESIZE_ICON[1], function ($constraint) {
                    $constraint->aspectRatio();
                })->save($path));

            Storage::disk(self::DEFAULT_FILE_DRIVER)
                ->putFileAs('product-categories', $image, $iconname, 'public');

            //delete file tmp
            Storage::delete('public/product-categories/' . $iconname);

            $category->icon = $this->completeImageLink('product-categories/'.$iconname);
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

            //tmp file in storage local
            $path = storage_path('app/public')."/product-categories/".$backgroundname;
            //resize image
            $image = new ImageFile(Image::make($background->path())
                ->resize(self::RESIZE_BACKGROUND[0],self::RESIZE_BACKGROUND[1], function ($constraint) {
                    $constraint->aspectRatio();
                })->save($path));

            Storage::disk(self::DEFAULT_FILE_DRIVER)
                ->putFileAs('product-categories', $image, $backgroundname, 'public');

            //delete file tmp
            Storage::delete('public/product-categories/' . $backgroundname);

            $category->background = $this->completeImageLink('product-categories/'.$backgroundname);
        }

		$category->save();

		return $category;
	}

	public function updateCategory($request, $id)
	{
		$randomName = strtolower(str_random(10));
		$category = $this->getCategoryById($id);
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

            //tmp file in storage local
            $path = storage_path('app/public')."/product-categories/".$iconname;
            //resize image
            $image = new ImageFile(Image::make($icon->path())
                ->resize(self::RESIZE_ICON[0],self::RESIZE_ICON[1])
                ->save($path));

            Storage::disk(self::DEFAULT_FILE_DRIVER)
                ->putFileAs('product-categories', $image, $iconname, 'public');

            //delete file tmp
            Storage::delete('public/product-categories/' . $iconname);

            $category->icon = $this->completeImageLink('product-categories/'.$iconname);
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

            //tmp file in storage local
            $path = storage_path('app/public')."/product-categories/".$backgroundname;
            //resize image
            $image = new ImageFile(Image::make($background->path())
                ->resize(self::RESIZE_BACKGROUND[0],self::RESIZE_BACKGROUND[1])
                ->save($path));

            Storage::disk(self::DEFAULT_FILE_DRIVER)
                ->putFileAs('product-categories', $image, $backgroundname, 'public');

            //delete file tmp
            Storage::delete('public/product-categories/' . $backgroundname);

            $category->background = $this->completeImageLink('product-categories/'.$backgroundname);
        }

        $category->update();

        return $category;
	}

    /**
     * @param $filename
     * @return string
     */
    protected function completeImageLink($filename)
    {
        return $this->s3Url . '' . $filename;
    }
}