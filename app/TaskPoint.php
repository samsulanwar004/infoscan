<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TaskPoint extends Model
{
    protected $table = 'task_points';

    public function task()
    {
    	return $this->belongsTo(Task::class, 'task_id');
    }
}
