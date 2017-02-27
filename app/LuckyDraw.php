<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LuckyDraw extends Model
{
	use SoftDeletes;
    protected $table = 'lucky_draws';

    public function luckyDraws()
    {
        return $this->belongsToMany(luckyDraw::class, 'members_lucky_draws', 'member_id', 'luckydraw_id');
    }
}
