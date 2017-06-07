<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BonusLevelPoint extends Model
{
    protected $table = 'bonus_level_points';

    public function levelPoint()
    {
        return $this->belongsTo(TaskLevelPoint::class, 'level_id');
    }

    public function bonusPoint()
    {
        return $this->belongsTo(BonusPoint::class, 'bonus_point_id');
    }
}
