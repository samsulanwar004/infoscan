<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserActivities extends Model
{
    protected $table = 'users_activities';

    public function user()
    {
    	return $this->belongsTo(User::class, 'user_id');
    }
}
