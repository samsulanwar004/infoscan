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

    public function taskLevels()
    {
        return $this->hasMany(TaskLevelPoints::class, 'task_id', 'id');
    }

    public function limit()
    {
        return $this->hasMany(LimitPoint::class, 'task_id', 'id');
    }

    protected static function boot() {
        parent::boot();

        static::deleting(function($task) { 
             $task->taskLevels()->delete();
             $task->limit()->delete();
        });
    }
}
