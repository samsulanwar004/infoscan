<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TaskLevelPoint extends Model
{
    protected $table = 'level_points';

    public function tasks()
    {
        return $this->belongsToMany(Task::class, 'tasks_level_points', 'level_id', 'task_id')
                    ->withPivot('point')
                    ->withTimestamps();
    }
}
