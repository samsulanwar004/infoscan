<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PromoPoint extends Model
{
    protected $table = 'promo_points';

    public function levels()
    {
        return $this->belongsToMany(TaskLevelPoint::class, 'promo_level_points', 'promo_point_id', 'level_id')
                    ->withPivot('point')
                    ->withTimestamps();
    }
}
