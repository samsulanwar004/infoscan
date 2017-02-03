<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MemberLevel extends Model
{
    protected $table = 'member_levels';

    public function member() 
    {
    	return $this->belongsTo(member::class, 'member_id');
    }
}
