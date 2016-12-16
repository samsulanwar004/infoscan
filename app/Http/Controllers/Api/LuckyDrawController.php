<?php

namespace App\Http\Controllers\Api;

use App\Services\LuckyDrawService;
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

}