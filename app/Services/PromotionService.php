<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Promotion;
use Auth;
use Carbon\Carbon;
use Cache;
use App\Transformers\PromotionTransformer;
use App\Transformers\CategoriesTransformer;
use App\Libraries\ImageFile;
use Image;
use Storage;
use App\ProductCategory;

class PromotionService
{
	const DEFAULT_FILE_DRIVER = 's3';
	const RESIZE_IMAGE = [240,null];
	/**
     * @var string
     */
	protected $date;

	/**
     * @var string
     */
	protected $s3Url;

	public function __construct()
	{
		$this->date = Carbon::now('Asia/Jakarta');
		$this->s3Url = env('S3_URL', '');
	}
	/**
     * @param $request
     * @param null $mi
     * @return bool
     */
	public function createPromotion(Request $request, $mi = null)
	{
		$date = $request->input('start_at');
		$dateArray = explode(' - ', $date);
		$randomName = strtolower(str_random(10));

		$p = new Promotion;
		$p->title = $request->input('title');
		$p->description = $request->input('description');
		$p->start_at = $dateArray[0];
		$p->end_at = $dateArray[1];
		$p->url = $request->input('url');
		$p->point = $request->input('point');
		$p->created_by = Auth::user()->id;
		$p->is_active = $request->has('is_active') ? 1 : 0;
		$p->merchant()->associate($mi);
		$p->category()->associate($request->input('category'));

		if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = sprintf(
                "%s-%s.%s",
                $randomName,
                date('Ymdhis'),
                $file->getClientOriginalExtension()
            );

            //tmp file in storage local
            $path = storage_path('app/public')."/promotions/".$filename;
            //resize image
            $image = new ImageFile(Image::make($file->path())
            	->resize(self::RESIZE_IMAGE[0], self::RESIZE_IMAGE[1], function ($constraint) {
            		$constraint->aspectRatio();
            	})->save($path));

            Storage::disk(self::DEFAULT_FILE_DRIVER)
            	->putFileAs('promotions', $image, $filename, 'public');

            //delete file tmp
            Storage::delete('public/promotions/' . $filename);

            $p->image = $this->completeImageLink('promotions/'.$filename);
        }

		$p->save();

		return $p;
	}

	/**
     * @param $request
     * @param $id
     * @param null $mi
     * @return bool
     */

	public function updatePromotion(Request $request, $id, $mi = null)
	{
		$date = $request->input('start_at');
		$dateArray = explode(' - ', $date);
		$randomName = strtolower(str_random(10));

		$p = $this->getPromotionById($id);
		$p->title = $request->input('title');
		$p->description = $request->input('description');
		$p->start_at = $dateArray[0];
		$p->end_at = $dateArray[1];
		$p->url = $request->input('url');
		$p->point = $request->input('point');
		$p->updated_by = Auth::user()->id;
		$p->is_active = $request->has('is_active') ? 1 : 0;
		$p->category_id = $request->input('category');

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = sprintf(
                "%s-%s.%s",
                $randomName,
                date('Ymdhis'),
                $file->getClientOriginalExtension()
            );

            //tmp file in storage local
            $path = storage_path('app/public')."/promotions/".$filename;
            //resize image
            $image = new ImageFile(Image::make($file->path())
            	->resize(self::RESIZE_IMAGE[0], self::RESIZE_IMAGE[1], function ($constraint) {
            		$constraint->aspectRatio();
            	})->save($path));

            Storage::disk(self::DEFAULT_FILE_DRIVER)
            	->putFileAs('promotions', $image, $filename, 'public');

            //delete file tmp
            Storage::delete('public/promotions/' . $filename);

            $p->image = $this->completeImageLink('promotions/'.$filename);
        }

		$p->update();

		return $p;
	}

	/**
     * @return mixed
     */
	public function getAllPromotion()
	{
		$p = Promotion::with('category')
			->where('is_active', '=', 1)
            ->orderBy('id', 'DESC')
            ->paginate(50);

 		return $p;
	}

	/**
     * @param  $mi
     * @return mixed
     */
	public function getPromotionByMerchantId($mi)
	{
		$p = Promotion::with('category')
			->with('merchant')
            ->where('merchant_id', '=', $mi->id)
            ->where('is_active', '=', 1)
            ->orderBy('promotions.id', 'DESC')
            ->paginate(50);

        return $p;
	}

	/**
     * @param  $id
     * @return mixed
     */
	public function getPromotionById($id)
	{
		return Promotion::where('id', '=', $id)->first();
	}

	/**
     * @return mixed
     */
	public function getApiAllPromotion()
	{
		if (Cache::has('promotion')) {
			return Cache::get('promotion');
		} else {

			$p = Promotion::with('category')
	            ->where('is_active', '=', 1)
				->where('start_at', '<=', $this->date)
				->where('end_at', '>=', $this->date)
	            ->get();

			$transform = fractal()
				->collection($p)
				->transformWith(new PromotionTransformer)
				->toArray();
			// save in promotion cache	
			//Cache::put('promotion', $transform, $this->date->addMinutes(10));

			return $transform;
		}

	}

	/**
     * @param $filename
     * @return string
     */
    protected function completeImageLink($filename)
    {
        return $this->s3Url . '' . $filename;
    }

    public function getApiCategories()
    {
    	$c = ProductCategory::get();

    	$transform = fractal()
			->collection($c)
			->transformWith(new CategoriesTransformer)
			->toArray();

		return $transform;
    }

}
