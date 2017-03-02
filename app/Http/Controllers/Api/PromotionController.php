<?php

namespace App\Http\Controllers\Api;

use App\Services\PromotionService;
use Exception;
use Illuminate\Http\Request;

class PromotionController extends BaseApiController
{
	public function index(Request $request)
	{
		try {

		    $category = $request->query('cat', null);
			$promos = (new PromotionService)->getApiAllPromotion($category);

			return $this->success($promos, 200);
		} catch (Exception $e) {
			return $this->error($e);
		}
	}

	public function categories()
	{
		try {
			$categories = (new PromotionService)->getApiCategories();

			return $this->success($categories, 200);
		} catch (Exception $e) {
			return $this->error($e);
		}
	}

}