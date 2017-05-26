<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BonusPoint extends Model
{
    protected $table = 'bonus_points';

    public function levels()
    {
    	return $this->belongsToMany(TaskLevelPoint::class, 'bonus_level_points', 'bonus_point_id', 'level_id')
                    ->withPivot('point')
                    ->withTimestamps();
    }
}
