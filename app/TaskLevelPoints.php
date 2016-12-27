<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TaskLevelPoints extends Model
{
    protected $table = 'tasks_level_points';

    public function levelPoint()
    {
        return $this->belongsTo(TaskLevelPoint::class, 'level_id');
    }

    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id');
    }
}
