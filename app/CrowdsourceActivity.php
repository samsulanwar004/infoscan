<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CrowdsourceActivity extends Model
{
    protected $table = 'crowdsource_activities';

    public function user()
    {
    	return $this->belongsTo(User::class, 'user_id');
    }
}
