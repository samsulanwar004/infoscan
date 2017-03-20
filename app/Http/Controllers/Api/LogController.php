<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Jobs\MemberActionJob;

class LogController extends BaseApiController
{

	private $member;

    public function __construct()
    {
    	$this->member = auth('api')->user();
    }

    public function store(Request $request)
    {
    	try {
            if(! $this->member) {
                return $this->error('Member not found', 400, true);
            }

    		//build data for member history
	        $memberId = $this->member->id;
	        $content = [
	            'type' => $request->input('type'),
	            'title' => $request->input('title'),
	            'description' => $request->input('description'),
	        ];

	        $config = config('common.queue_list.member_action_log');
	        $job = (new MemberActionJob($memberId, $request->input('group'), $content))->onQueue($config)->onConnection(env('INFOSCAN_QUEUE'));
	        dispatch($job);

	        return $this->success();
    	} catch (\Exception $e) {
    		return $this->error($e, 400, true);
    	}
    }
}
