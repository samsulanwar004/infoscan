<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use App\Services\MemberService;

class LeaderboardController extends AdminController
{
    public function index()
    {
        $leader = (new MemberService)->getLeaderboard();
        $leader = $leader->filter(function($value) {
            return $value->rank <= 10;
        });

        return view('leaderboard.index', compact('leader'));
    }
}
