<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MemberReferral extends Model
{
    protected $table = 'member_referrals';

    public function referral()
    {
        return $this->belongsTo(Member::class, 'member_id_referral');
    }

    public function referrer()
    {
        return $this->belongsTo(Member::class, 'member_id_referrer');
    }

    public function referralPoint()
    {
        return $this->belongsTo(Referral::class, 'referral_point_id');
    }
}
