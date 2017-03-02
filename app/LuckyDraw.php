<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LuckyDraw extends Model
{
	use SoftDeletes;
    protected $table = 'lucky_draws';

    public function memberLuckyDraws()
    {
        return $this->hasMany(MemberLuckyDraw::class, 'luckydraw_id', 'id');
    }
    
}
