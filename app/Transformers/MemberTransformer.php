<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Services\PointService;

class MemberTransformer extends TransformerAbstract
{
    public function transform($member)
    {
        $me = (new PointService)->getDetailMe($member);
    	$backAccount = is_null($member->bank_account) ?:decrypt($member->bank_account);

        $member = [
            'name'   => $member->name,
            'email'  => $member->email,
            'avatar' => $member->avatar,
            'bank_account' => null === $backAccount ?:$backAccount['bank_account'],
            'account_number' => null === $backAccount ?:substr($backAccount['account_number'], 0, 3) . '-xxx-xxxx',
        ];

        return array_merge($member, $me);
    }
}