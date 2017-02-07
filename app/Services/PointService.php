<?php

namespace App\Services;

use App\Task;
use App\TaskLevelPoint;
use App\TaskLevelPoints;
use App\PromoPoint;
use App\PromoLevelPoint;
use App\SnapTag;
use DB;
use Carbon\Carbon;

class PointService
{

    const CACHE_NAME = 'point.pivot';
    const CACHE_PROMO_NAME = 'promo.point.pivot';

    public function __construct()
    {
        $this->date = Carbon::now('Asia/Jakarta');
    }

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

        $points = DB::select('select t.id, t.name as task_name, l.name as level_name, tlp.point from tasks_level_points as tlp
inner join tasks as t on t.id = tlp.task_id
inner join level_points as l on l.id = tlp.level_id;');
        $result = [];
        foreach ($points as $pivot) {
            $result[] = [
                'Task' => $pivot->id.' '.$pivot->task_name,
                'Level' => $pivot->level_name,
                'Point' => $pivot->point,
            ];
        }

        cache()->put(self::CACHE_NAME, $result, 1440);

        return $result;
    }

    public function getPromoPivotGrid()
    {
        if($pivots = cache(self::CACHE_PROMO_NAME)) {
            return $pivots;
        }

        $points = DB::select('select c.id, c.city_name as city_name, c.is_active, l.name as level_name, plp.point from promo_level_points as plp
inner join promo_points as c on c.id = plp.promo_point_id
inner join level_points as l on l.id = plp.level_id;');
        $result = [];
        foreach ($points as $pivot) {
            $active = ($pivot->is_active == 0) ? '(Deactive)' : '';
            $result[] = [
                'City' => $pivot->id.' '.$pivot->city_name.' '.$active,
                'Level' => $pivot->level_name,
                'Point' => $pivot->point,
            ];
        }

        cache()->put(self::CACHE_PROMO_NAME, $result, 1440);

        return $result;
    }

    public function getLevels()
    {
        return TaskLevelPoint::orderBy('id', 'asc')->get(['id', 'name', 'point']);
    }

    protected function removeCache()
    {
        cache()->forget(self::CACHE_NAME);

        cache()->forget(self::CACHE_PROMO_NAME);

        return;
    }

    public function addLevel($name)
    {
        $level = new TaskLevelPoint;
        $level->name = $name;
        $level->save();

        return $level;
    }

    public function addTask($request)
    {
        $task = new Task;
        $task->name = $request['name'];
        $task->code = sprintf("%s%s", $request['task_type'], $request['task_mode']);
        $task->save();

        return $task;
    }

    public function addCityPromo($request)
    {
        $date = $request['start_at'];
        $dateArray = explode(' - ', $date);
        $promo = new PromoPoint;
        $promo->city_name = $request['name'];
        $promo->point_city = $request['point_city'];
        $promo->start_at = $dateArray[0];
        $promo->end_at = $dateArray[1];

        $promo->save();

        return $promo;
    }

    public function updateTask($request, $id)
    {
        $task = $this->getTaskById($id);
        $task->name = $request->input('name');
        $task->code = ($request['task_type'] == 0) ? $task->code : sprintf("%s%s", $request['task_type'], $request['task_mode']);
        $task->update();

        return $task;
    }

    public function updatePromoPoint($request, $id)
    {
        $date = $request->input('start_at');
        $dateArray = explode(' - ', $date);
        $promo = $this->getPromoPointById($id);
        $promo->city_name = $request->input('name');
        $promo->point_city = $request->input('point_city');
        $promo->start_at = $dateArray[0];
        $promo->end_at = $dateArray[1];
        $promo->is_active = $request->has('is_active') ? 1 : 0;

        $promo->update();

        return $promo;
    }

    public function addTaskLevelPoint($request)
    {
        DB::beginTransaction();

        $task = $this->addTask($request->all());

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
            $levelId = [];
            foreach ($request->input('levels') as $levelName => $point) {
                $level = $this->findLevel($levelName);
                $levelId[] = $level->id;
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
            // delete unnesesary level
            TaskLevelPoints::where('task_id', '=', $id)
                    ->whereNotIn('level_id', $levelId)->delete();

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

    public function getPromoLevelPoints($promoPointid, $levelid)
    {
        $plp = PromoLevelPoint::where('promo_point_id', '=', $promoPointid)
            ->where('level_id', '=', $levelid)
            ->first();

        return $plp;
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

    public function getPromoPointById($id)
    {
        $t = PromoPoint::with('levels')
            ->where('id', '=', $id)
            ->first();

        return $t;
    }

    public function addPromoLevelPoint($request)
    {
        DB::beginTransaction();

        $promo = $this->addCityPromo($request->all());

        if ($promo) {
            foreach ($request->input('levels') as $levelName => $point) {
                $promoLevelPoint = new PromoLevelPoint;
                $promoLevelPoint->point = $point;
                $level = $this->findLevel($levelName);
                $promoLevelPoint->levelPoint()->associate($level);
                $promoLevelPoint->promoPoint()->associate($promo);
                $promoLevelPoint->save();
            }

            $this->removeCache();

            DB::commit();

            return true;
        }

        DB::rollBack();

        return false;
    }

    public function updatePromoLevelPoint($request, $id)
    {
        DB::beginTransaction();

        $promo = $this->updatePromoPoint($request, $id);

        if ($promo) {
            $levelId = [];
            foreach ($request->input('levels') as $levelName => $point) {
                $level = $this->findLevel($levelName);
                $levelId[] = $level->id;
                $promoLevelPoint = $this->getPromoLevelPoints($promo->id, $level->id);   

                if ($promoLevelPoint == false)
                {
                    $newPromoLevelPoint = new PromoLevelPoint;
                    $newPromoLevelPoint->point = $point;
                    $newPromoLevelPoint->levelPoint()->associate($level);
                    $newPromoLevelPoint->promoPoint()->associate($promo);
                    $newPromoLevelPoint->save();
                } else {
                    $promoLevelPoint->point = $point;
                    $promoLevelPoint->update();
                }

            }
            // delete unnesesary level
            PromoLevelPoint::where('promo_point_id', '=', $id)
                    ->whereNotIn('level_id', $levelId)->delete();

            $this->removeCache();

            DB::commit();

            return true;
        }

        DB::rollBack();

        return false;        
    }

    public function updatePointManager($request)
    {
        foreach ($request->input('levels') as $levelName => $point) {
            $level = $this->findLevel($levelName);
            $l = $this->getLevelById($level->id);
            $l->point = $point;
            $l->update();
        }

    }

    public function getLevelById($id)
    {
        return TaskLevelPoint::where('id', $id)->first();
    }

    /**
    * Get calculate Estimated point
    * @param integer $memberId
    * @param string $type
    * @param string $mode
    * @return integer point
    */
    public function calculateEstimatedPoint($memberId, $type, $mode)
    {

        $type = $this->getTypeId($type);
        $tag = true;
        $mode = $this->getModeId($mode, $tag);      

        $status = '1';

        $code = $type.$mode.$status;

        $levelId = (new MemberService)->getLevelIdByMemberId($memberId);

        $point = \DB::table('tasks')
            ->join('tasks_level_points', 'tasks.id', '=', 'tasks_level_points.task_id')
            ->join('level_points', 'level_points.id', '=', 'tasks_level_points.level_id')
            ->select('tasks_level_points.point')
            ->where('tasks.code', $code)
            ->where('tasks_level_points.level_id', $levelId)
            ->first();

        return ($point != null) ? $point->point : '0';
    }

    /**
    * Get calculate Promo point
    * @param integer $memberId
    * @param string $city
    * @return array
    */
    public function calculatePromoPoint($memberId, $city)
    {
        $levelId = (new MemberService)->getLevelIdByMemberId($memberId);
        $cityName = strtoupper($city);
        $cityPromo = \DB::table('promo_points')
            ->join('promo_level_points', 'promo_points.id', '=', 'promo_level_points.promo_point_id')
            ->join('level_points', 'level_points.id', '=', 'promo_level_points.level_id')
            ->select('promo_points.point_city', 'promo_level_points.point')
            ->where('promo_points.city_name', $cityName)
            ->where('promo_level_points.level_id', $levelId)
            ->where('start_at', '<=', $this->date)
            ->where('end_at', '>=', $this->date)
            ->where('is_active', '1')
            ->first();

        $data = [
            'point_city' => isset($cityPromo) ? $cityPromo->point_city : 0,
            'point_level_city' => isset($cityPromo) ? $cityPromo->point : 0,
        ];

        return $data;
    }

    public function calculateApprovePoint($snaps)
    {
        $memberId = $snaps->member_id;
        $type = $snaps->snap_type;
        $mode = $snaps->mode_type;
        $city = $snaps->outlet_city;
        $files = $snaps->files;

        $calculateTask = $this->calculateEstimatedPoint($memberId, $type, $mode);

        $calculatePromo = $this->calculatePromoPoint($memberId, $city);

        $point = [];
        foreach ($files as $file) {
            $file = (new SnapService)->getSnapFileById($file->id);
            $status = [];
            foreach ($file->tag as $tag) {
                $status[] = $this->checkTagStatus($tag->current_signature, $tag->edited_signature);
            }
            $count = array_count_values($status);
            $memberAdd = isset($count['member_add']) ? $count['member_add'] : 0;
            $crowdSourceEdit = isset($count['crowdsource_edit']) ? $count['crowdsource_edit'] : 0;
            $crowdSourceAdd = isset($count['crowdsource_add']) ? $count['crowdsource_add'] : 0;
            $point[] = $memberAdd / ($memberAdd + $crowdSourceEdit + $crowdSourceAdd) * ($calculateTask + $calculatePromo['point_city'] + $calculatePromo['point_level_city']);

        }

        $totalPoint = round(collect($point)->sum());

        return $totalPoint;
    }

    public function checkTagStatus($currentSignature, $editedSignature)
    {
        if ($currentSignature == null) {
            return 'crowdsource_add';
        } elseif ($currentSignature == $editedSignature || $editedSignature == null) {
            return 'member_add';
        } elseif ($currentSignature != $editedSignature) {
            return 'crowdsource_edit';
        }
    }

    public function calculatePoint($memberId, $type, $mode, $fileId)
    {

        $type = $this->getTypeId($type);

        $tag = $this->checkTags($fileId);

        $mode = $this->getModeId($mode, $tag);      

        $status = ($tag == true) ? '1' : '0';

        $code = $type.$mode.$status;

        $levelId = (new MemberService)->getLevelIdByMemberId($memberId);

        $point = \DB::table('tasks')
            ->join('tasks_level_points', 'tasks.id', '=', 'tasks_level_points.task_id')
            ->join('level_points', 'level_points.id', '=', 'tasks_level_points.level_id')
            ->select('tasks_level_points.point')
            ->where('tasks.code', $code)
            ->where('tasks_level_points.level_id', $levelId)
            ->first();

        return $point;
    }

    protected function checkTags($fileId)
    {
        return SnapTag::where('snap_file_id', $fileId)->first();
    }

    protected function getTypeId($type)
    {
        $type = strtolower($type);
        if ($type == 'receipt') {
            $type = 'a';
        } elseif ($type == 'handwritten') {
            $type = 'b';
        } elseif ($type == 'generaltrade') {
            $type = 'c';
        }

        return $type;
    }

    protected function getModeId($mode, $tag)
    {
        switch ($mode) {
            case 'audios':
                $with = ($tag == true) ? '1' : '2';
                $mode = '1'.$with;
                break;

            case 'tags':
                $with = ($tag == true) ? '1' : '2';
                $mode = '2'.$with;
                break;

            case 'input':
                $with = ($tag == true) ? '1' : '2';
                $mode = '3'.$with;
                break;
            
            default:
                $with = ($tag == true) ? '1' : '2';
                $mode = '4'.$with;
                break;
        }

        return $mode;
    }

}
