<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LuckyDrawWinner extends Model
{
    protected $table = 'luckydraw_winners';

    public function member()
    {
    	return $this->belongsTo(Member::class, 'member_id');
    }

    public function luckydraw()
    {
    	return $this->belongsTo(LuckyDraw::class, 'luckydraw_id');
    }
}
