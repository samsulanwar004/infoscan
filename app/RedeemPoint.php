<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RedeemPoint extends Model
{
    protected $table = 'redeem_point';

    public function member()
    {
    	return $this->belongsTo(Member::class, 'member_id');
    }
}
