<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Promotion;
use Auth;

class PromotionService
{
	/**
     * @param $request
     * @param null $id
     * @param null $mi
     * @return bool
     */
	public function persistPromotion(Request $request, $mi = null, $id = null)
	{
		$date = $request->input('start_at');
		$dateArray = explode(' - ', $date);

		$p = is_null($id) ? new Promotion : $this->getPromotionById($id);
		$p->title = $request->input('title');
		$p->description = $request->input('description');
		$p->start_at = $dateArray[0];
		$p->end_at = $dateArray[1];
		$p->url = $request->input('url');
		$p->created_by = isset($id) ? $p->created_by : Auth::user()->id;
		$p->updated_by = isset($id) ? Auth::user()->id : null;
		$p->is_active = $request->has('is_active') ? 1 : 0;
		$p->merchant_id = isset($mi) ? $mi : null;
		$p->save();

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
            ->where('merchant_id', '=', $mi)
            ->where('is_active', '=', 1)
            ->orderBy('promotions.id', 'DESC')
            ->paginate(50);

        return $p;
	}

	private function getPromotionById($id)
	{
		return Promotion::where('id', '=', $id)->first();
	}

}
