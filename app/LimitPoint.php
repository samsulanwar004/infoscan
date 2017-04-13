<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LimitPoint extends Model
{
    protected $table = 'limit_points';

    public function task()
    {
    	return $this->belongsTo(Task::class, 'task_id');
    }
}
