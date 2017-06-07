<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MemberLevel extends Model
{
    protected $table = 'member_levels';

    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id');
    }

    public function level()
    {
        return $this->belongsTo(TaskLevelPoint::class, 'level_id');
    }
}
