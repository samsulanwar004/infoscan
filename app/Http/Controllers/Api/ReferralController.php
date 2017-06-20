<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Member;

class ReferralController extends BaseApiController
{
    public function index()
    {
        try {
            $member = Member::whereNull('referral_me')
                ->take(10)
                ->get();

            if ($member) {
                foreach ($member as $key => $value) {
                    $m = $this->getMemberById($value->id);
                    $name = explode(' ', str_replace(str_split('\\/:*?"<>|@.,'), ' ', $m->name));
                    $m->referral_me = strtolower($name[0].rand(10000,99999));
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
