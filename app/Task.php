<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $table = 'tasks';

    public function levels()
    {
        return $this->belongsToMany(TaskLevelPoint::class, 'tasks_level_points', 'task_id', 'level_id')
                    ->withPivot('point')
                    ->withTimestamps();
    }
}
