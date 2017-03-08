<?php

namespace App\Http\Controllers\Web;

use App\Member;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MemberController extends AdminController
{
	public function show(Request $request, $id)
	{
		$member = $this->getMemberById($id);

		return view('members.show', compact('member'));
	}

	private function getMemberById($id)
	{
		return Member::where('id', $id)->first();
	}

	public function verify($token)
    {
        $founded = false;
        if($m = Member::getByVerificationToken($token)->first()) {
	        if($m->verification_expired->gte(Carbon::now())) {
	        	$m->is_verified = 1;
		        $m->verification_token = null;
		        $m->verification_expired = null;
		        $m->save();

	        	$founded = true;
	        }
        }


        return view('auth.verification', compact('founded'));
    }
}