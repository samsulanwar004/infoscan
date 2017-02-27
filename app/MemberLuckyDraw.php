<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MemberLuckyDraw extends Model
{
    protected $table = 'members_lucky_draws';

    public function member()
    {
    	return $this->belongsTo(Member::class, 'member_id');
    }

    public function luckydraw()
    {
    	return $this->belongsTo(LuckyDraw::class, 'luckydraw_id');
    }
}
