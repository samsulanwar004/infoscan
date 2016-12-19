<?php

namespace App\Http\Controllers\Web;

use App\Member;
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
}