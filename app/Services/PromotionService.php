<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Promotion;
use Auth;
use Carbon\Carbon;
use Cache;
use App\Transformers\PromotionTransformer;

class PromotionService
{
	/**
     * @var string
     */
	protected $date;

	public function __construct()
	{
		$this->date = Carbon::now('Asia/Jakarta');
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

		$p = new Promotion;
		$p->title = $request->input('title');
		$p->description = $request->input('description');
		$p->start_at = $dateArray[0];
		$p->end_at = $dateArray[1];
		$p->url = $request->input('url');
		$p->created_by = Auth::user()->id;
		$p->is_active = $request->has('is_active') ? 1 : 0;
		$p->merchant()->associate($mi);
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

		$p = $this->getPromotionById($id);
		$p->title = $request->input('title');
		$p->description = $request->input('description');
		$p->start_at = $dateArray[0];
		$p->end_at = $dateArray[1];
		$p->url = $request->input('url');
		$p->updated_by = Auth::user()->id;
		$p->is_active = $request->has('is_active') ? 1 : 0;
		$p->update();

		return $p;
	}

	/**
     * @return mixed
     */
	public function getAllPromotion()
	{
		$p = Promotion::where('is_active', '=', 1)
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
		$p = DB::table('merchants')
            ->join('promotions', 'merchants.id', '=', 'promotions.merchant_id')
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

			$p = Promotion::where('is_active', '=', 1)
				->where('start_at', '<=', $this->date)
				->where('end_at', '>=', $this->date)
				->get();

			$transform = fractal()
				->collection($p)
				->transformWith(new PromotionTransformer)
				->toArray();
			// save in promotion cache	
			Cache::put('promotion', $transform, $this->date->addMinutes(10));

			return $transform;
		}

	}

}
