<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;

class MemberTransformer extends TransformerAbstract
{
    public function transform($member)
    {
        return [
            'code'   => $member->member_code,
            'name'   => $member->name,
            'email'  => $member->email,
            'avatar' => $member->avatar,
        ];
    }
}