<?php

namespace App\Services;

use Illuminate\Http\Request;
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

	private function getPromotionById($id)
	{
		return Promotion::where('id', '=', $id)->first();
	}

}
