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

        $points = DB::select('select t.id as id, t.name as task_name, l.name as level_name, tlp.point from tasks_level_points as tlp
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

        //cache()->put(self::CACHE_NAME, $result, 1440);

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
        $level->save();

        return $level;
    }

    public function addTask($name)
    {
        $task = new Task;
        $task->name = $name;
        $task->save();

        return $task;
    }

    public function updateTask($request, $id)
    {
        $task = $this->getTaskById($id);
        $task->name = $request->input('name');
        $task->update();

        return $task;
    }

    public function addTaskLevelPoint($request)
    {
        DB::beginTransaction();

        $task = $this->addTask($request->input('name'));

        if ($task) {
            foreach ($request->input('levels') as $levelName => $point) {
                $taskLevelPoint = new TaskLevelPoints;
                $taskLevelPoint->point = $point;
                $level = $this->findLevel($levelName);
                $taskLevelPoint->levelPoint()->associate($level);
                $taskLevelPoint->task()->associate($task);
                $taskLevelPoint->save();
            }

            $this->removeCache();

            DB::commit();

            return true;
        }

        DB::rollBack();

        return false;        
    }

    public function updateTaskLevelPoint($request, $id)
    {
        DB::beginTransaction();

        $task = $this->updateTask($request, $id);

        if ($task) {
            foreach ($request->input('levels') as $levelName => $point) {
                $level = $this->findLevel($levelName);
                $taskLevelPoint = $this->getTaskLevelPoints($task->id, $level->id);   

                if ($taskLevelPoint == false)
                {
                    $newTaskLevelPoint = new TaskLevelPoints;
                    $newTaskLevelPoint->point = $point;
                    $newTaskLevelPoint->levelPoint()->associate($level);
                    $newTaskLevelPoint->task()->associate($task);
                    $newTaskLevelPoint->save();
                } else {
                    $taskLevelPoint->point = $point;
                    $taskLevelPoint->update();
                }
                
            }

            $this->removeCache();

            DB::commit();

            return true;
        }

        DB::rollBack();

        return false;        
    }

    public function getTaskLevelPoints($taskid, $levelid)
    {
        $tlp = TaskLevelPoints::where('task_id', '=', $taskid)
            ->where('level_id', '=', $levelid)
            ->first();

        return $tlp;
    }

    public function lastLevel()
    {
        return TaskLevelPoint::orderBy('id', 'desc')->first();
    }

    public function getLevelByName($name)
    {
        return TaskLevelPoint::where('name', '=', 'Level ' . $name)->first();
    }

    public function findLevel($levelName)
    {
        if ($level = $this->getLevelByName($levelName))
        {
            return $level;
        } else {
            $name = 'Level ' . $levelName;
            $level = $this->addLevel($name);

            return $level;
        }
    }

    public function getTaskById($id)
    {
        $t = Task::with('levels')
            ->where('id', '=', $id)
            ->first();

        return $t;
    }

}
