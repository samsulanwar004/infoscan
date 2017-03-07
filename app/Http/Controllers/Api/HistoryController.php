<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Services\TransactionService;

class HistoryController extends BaseApiController
{
    public function index()
    {
    	try {
    		$member = auth('api')->user();
    		$history = (new TransactionService)->getHistoryTransaction($member);

    		return $this->success($history, 200);
    	} catch (\Exception $e) {
    		return $this->error($e, 400, true);
    	}
    }
}
