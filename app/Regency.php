<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Regency extends Model
{
    protected $table = 'regencies';

    public function province()
    {
    	return $this->belongsTo(Province::class, 'province_id');
    }
}
