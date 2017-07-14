<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Member;

class ReferralController extends BaseApiController
{
    public function index()
    {
        try {

            $member = Member::get();

            if ($member) {
                foreach ($member as $key => $value) {
                    $m = $this->getMemberById($value->id);
                    $m->referral_me = unique_random('members', 'referral_me', 6);
                    $m->update();
                }
            }

            $data = [
                'count' => count($member),
            ];

            return $this->success($data, 200, true);
        } catch (\Exception $e) {
            return $this->error($e, 400, true);
        }
    }

    private function getMemberById($id)
    {
        return Member::where('id', $id)
            ->first();
    }
}
