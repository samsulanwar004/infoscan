<?php

namespace App\Services;

use App\Task;
use App\TaskLevelPoint;
use App\TaskLevelPoints;
use DB;

class PointService
{

    const CACHE_NAME = 'point.pivot';

    /**
     * Get data and reformat data for pivot table
     *
     * @return array|null
     */
    public function getPivotGrid()
    {
        if($pivots = cache(self::CACHE_NAME)) {
            return $pivots;
        }

        $points = DB::select('select t.name as task_name, l.name as level_name, tlp.point from tasks_level_points as tlp
inner join tasks as t on t.id = tlp.task_id
inner join level_points as l on l.id = tlp.level_id;');
        $result = [];
        foreach ($points as $pivot) {
            $result[] = [
                'Task' => $pivot->task_name,
                'Level' => $pivot->level_name,
                'Point' => $pivot->point,
            ];
        }

        cache()->put(self::CACHE_NAME, $result, 1440);

        return $result;
    }

    public function getLevels()
    {
        return TaskLevelPoint::orderBy('id', 'asc')->get(['id', 'name']);
    }

    protected function removeCache()
    {
        cache()->forget(self::CACHE_NAME);

        return;
    }

    public function addLevel($name)
    {
        $level = new TaskLevelPoint;
        $level->name = $name;

        return $level->save();
    }

    public function addTask($name)
    {
        $task = new Task;
        $task->name = $name;
        $task->save();

        return $task;
    }

    public function addTaskLevelPoint($request)
    {
        DB::beginTransaction();

        $task = $this->addTask($request->input('name'));

        if ($task) {
            foreach ($request->input('levels') as $level => $point) {
                $taskLevelPoint = new TaskLevelPoints;
                $taskLevelPoint->point = $point;
                $taskLevelPoint->levelPoint()->associate($this->getLevelById($level));
                $taskLevelPoint->task()->associate($task);
                $taskLevelPoint->save();
            }

            DB::commit();

            return true;
        }

        DB::rollBack();

        return false;        
    }

    public function getLevelById($id)
    {
        return TaskLevelPoint::where('id', '=', $id)->first();
    }

}
