<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Services\MemberService;

class LeaderboardController extends BaseApiController
{

	protected $member;

	public function __construct()
	{
		$this->member = auth('api')->user();
	}

    public function index()
    {
    	$memberId = $this->member->id;
    	try {
    		$leader = (new MemberService)->getLeaderboard();
			$member = $leader->filter(function($value) use ($memberId) {
				return $value->id == $memberId;
			});

			foreach ($member as $key => $value) {
				$me = [
					'rank' => $value->rank,
					'score' => $value->score,
				];
			}

    		$leader = $leader->filter(function($value) {
				return $value->rank <= 10;
			});

    		$leaderboard[] = [
	    		'me' => $me,
	    		'ranking' => $leader,
	    	];

    		return $this->success($leaderboard, 200);
    	} catch (\Exception $e) {
    		return $this->error($e, 400, true);
    	}
    }
}
