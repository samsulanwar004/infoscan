<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;

class MemberTransformer extends TransformerAbstract
{
    public function transform($member)
    {
    	$backAccount = is_null($member->bank_account) ?:decrypt($member->bank_account);

        return [
            //'code'   => $member->member_code,
            'member_name'   => $member->name,
            'member_email'  => $member->email,
            'avatar' => $member->avatar,
            'bank_account' => null === $backAccount ?:$backAccount['bank_account'],
            'account_number' => null === $backAccount ?:substr($backAccount['account_number'], 0, 3) . '-xxx-xxxx',
        ];
    }
}