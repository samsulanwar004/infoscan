<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PromoLevelPoint extends Model
{
    protected $table = 'promo_level_points';

    public function levelPoint()
    {
        return $this->belongsTo(TaskLevelPoint::class, 'level_id');
    }

    public function promoPoint()
    {
        return $this->belongsTo(PromoPoint::class, 'promo_point_id');
    }
}
