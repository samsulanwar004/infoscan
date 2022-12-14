<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    protected $table = 'provinces';

    public function regencies()
    {
    	return $this->hasMany(Regency::class, 'province_id', 'id');
    }
}
