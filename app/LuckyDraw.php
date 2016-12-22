<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LuckyDraw extends Model
{
	use SoftDeletes;
    protected $table = 'lucky_draws';

    public function members()
    {
        return $this->belongsToMany(Member::class, 'members_lucky_draws', 'luckydraw_id', 'member_id');
    }
}
