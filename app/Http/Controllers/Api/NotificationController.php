<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Services\TransactionService;

class NotificationController extends BaseApiController
{

	private $member;

	public function __construct()
	{
		$this->member = auth('api')->user();
	}

    public function index()
    {
    	try {
    		$notification = (new TransactionService)->getNotification($this->member);

    		return $this->success($notification, 200);
    	} catch (\Exception $e) {
    		return $this->error($e, 400, true);
    	}
    }

}
