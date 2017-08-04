<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MemberReward extends Model
{
    protected $table = 'member_rewards';
    public $timestamps = false;

    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id');
    }
}
