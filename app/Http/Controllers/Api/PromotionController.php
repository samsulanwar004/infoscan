<?php

namespace App\Http\Controllers\Api;

use App\Services\PromotionService;
use Exception;

class PromotionController extends BaseApiController 
{
	public function index()
	{
		try {

			$promos = (new PromotionService)->getApiAllPromotion();
				
			return $this->success($promos, 200);
		} catch (Exception $e) {
			return $this->error($e);
		}
	}

}