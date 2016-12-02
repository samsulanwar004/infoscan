<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LuckyDraw extends Model
{
    protected $table = 'lucky_draws';

    public function members()
    {
        return $this->belongsToMany(Member::class, 'members_lucky_draws', 'luckydraw_id', 'member_id');
    }
}
