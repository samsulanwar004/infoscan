<?php

namespace App\Http\Controllers\Api;

use App\Services\LuckyDrawService;
use Illuminate\Http\Request;
use Exception;

class LuckyDrawController extends BaseApiController 
{
	public function index()
	{
		try {

			$luckys = (new LuckyDrawService)->getApiAllLuckyDraw();
			
			return response()->json($luckys, 200);
		} catch (Exception $e) {
			return $this->error($e);
		}
	}

	public function store(Request $request)
	{
		try {
			$lucky = (new LuckyDrawService)->redeemPoint($request);

			if ($lucky == "ok") {
				return $this->success();
			} else {
				return $this->error($lucky);
			}
			
		} catch (Exception $e) {
			logger($e);

            return $this->error($e);
		}
	}

}