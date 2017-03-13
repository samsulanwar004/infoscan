<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Exception;
use App\Services\PointService;

class PortalPointController extends BaseApiController
{
    public function index()
    {
    	try {
    		$member = auth('api')->user();
    		$point = (new PointService)->getPortalPoint($member);
    		
    		return $this->success($point, 200);
    	} catch (Exception $e) {
    		return $this->error($e, 400, true);
    	}
    }
}
