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
use App\Transformers\PortalPointTransformer;

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
        $task->percentage = (isset($request['percentage'])) ? $request['percentage'] : 0;
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
        $task->code = sprintf("%s%s", $request['task_type'], $request['task_mode']);
        $task->percentage = (isset($request['percentage'])) ? $request['percentage'] : 0;
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
    * @param integer $tags
    * @return integer point
    */
    public function calculateEstimatedPoint($memberId, $type, $mode, $tags)
    {

        $type = $this->getTypeId($type);
        $mode = ($tags <= 0) ? 'nomode' : $mode;
        $mode = $this->getModeId($mode);      

        $code = $type.$mode;

        $levelId = (new MemberService)->getLevelIdByMemberId($memberId);

        $point = \DB::table('tasks')
            ->join('tasks_level_points', 'tasks.id', '=', 'tasks_level_points.task_id')
            ->join('level_points', 'level_points.id', '=', 'tasks_level_points.level_id')
            ->select('tasks.percentage as percent', 'tasks_level_points.point as point')
            ->where('tasks.code', $code)
            ->where('tasks_level_points.level_id', $levelId)
            ->first();

        $data = [
            'percent' => isset($point) ? $point->percent : 0,
            'point' => isset($point) ? $point->point : 0,
        ];

        return $data;
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

        $tags = (new SnapService)->getCountOfTags($files);
        $memberTag = $tags['member_add'];

        $calculateTask = $this->calculateEstimatedPoint($memberId, $type, $mode, $memberTag);

        $calculatePromo = $this->calculatePromoPoint($memberId, $city);       

        $memberAdd = $tags['member_add'];
        $crowdSourceEdit = $tags['crowdsource_edit'];
        $crowdSourceAdd = $tags['crowdsource_add'];
        $totalTag = $memberAdd + $crowdSourceEdit + $crowdSourceAdd;
        
        if ($totalTag <= 0) {
            throw new \Exception("Data product notfound! add product before approve this content!", 1);                
        }

        $point = $memberAdd / $totalTag * ($calculateTask['point'] + $calculatePromo['point_city'] + $calculatePromo['point_level_city']);

        if ($type == 'receipt') {
            $point = $calculateTask['point'] + $calculatePromo['point_city'] + $calculatePromo['point_level_city'];
        } else {
            if ($point <= 0) {
                $task = $calculateTask['point'] + $calculatePromo['point_city'] + $calculatePromo['point_level_city'];
                $point = ($calculateTask['percent'] / 100) * $task;
            }
        }

        $totalPoint = round($point);

        return $totalPoint;
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

    protected function getModeId($mode)
    {
        switch ($mode) {
            case 'audios':
                $mode = '3';
                break;

            case 'tags':
                $mode = '2';
                break;

            case 'input':
                $mode = '2';
                break;
            
            default:
                $mode = '1';
                break;
        }

        return $mode;
    }

    public function getPortalPoint($memberCode)
    {
        $point = (new TransactionService)->getCreditMember($memberCode);

        $snaps = (new SnapService)->getSnapByMemberCode($memberCode);

        $notifications = $snaps->filter(function($value, $Key) {
            return $value->approved_by != null || $value->reject_by != null;
        });

        $notif = [];
        foreach ($notifications as $notification) {
            $notif[] = $this->getWording($notification);
        }

        $snaps = $snaps->filter(function($value, $Key) {
            return $value->approved_by == null && $value->reject_by == null;
        });

        $estimated = [];
        foreach ($snaps as $snap) {
            $estimated[] = $snap->estimated_point;
        }

        $estimated = collect($estimated)->sum();

        $data = [
            'current_point' => $point,
            'estimated_point' => $estimated,
            'description' => $notif,
        ];

        return $data;
    }

    private function getWording($notification)
    {
        $snapType = $notification->snap_type;        
        $dateTime = $notification->created_at->toDateTimeString();

        switch ($snapType) {
            case 'handWritten':
                $title = isset($notification->approved_by) ? 'Transaksi Nota tulis berhasil' : 'Transaksi Nota tulis gagal';
                $description = isset($notification->approved_by) ? 'Transaksi nota tulis kamu telah berhasil! Kluk!' : 'Transaksi nota tulis kamu belum berhasil :(';
                $status = isset($notification->approved_by) ? 1 : 0;

                $data = [
                    'title' => $title,
                    'description' => $description,
                    'status' => $status,
                    'date_time' => $dateTime,
                ];

                return $data;
                break;
            case 'generalTrade':
                $title = isset($notification->approved_by) ? 'Transaksi Warung berhasil' : 'Transaksi Warung gagal';
                $description = isset($notification->approved_by) ? 'Transaksi warung kamu telah berhasil! Kluk!' : 'Transaksi warung kamu belum berhasil :(';
                $status = isset($notification->approved_by) ? 1 : 0;

                $data = [
                    'title' => $title,
                    'description' => $description,
                    'status' => $status,
                    'date_time' => $dateTime,
                ];

                return $data;
                break;
                
            default:
                $title = isset($notification->approved_by) ? 'Transaksi Struk berhasil' : 'Transaksi Struk gagal';
                $description = isset($notification->approved_by) ? 'Transaksi struk belanja kamu telah berhasil! Kluk!' : 'Transaksi struk belanja kamu belum berhasil :(';
                $status = isset($notification->approved_by) ? 1 : 0;

                $data = [
                    'title' => $title,
                    'description' => $description,
                    'status' => $status,
                    'date_time' => $dateTime,
                ];

                return $data;
                break;
                
        }
    }

    public function getDetailMe($member)
    {
        $point = (new TransactionService)->getCreditMember($member->member_code);
        $memberService = (new MemberService)->getLevelByMemberId($member->id);
        $levelId = $memberService['level_id'];
        $latestPoint = $memberService['latest_point'];
        $level = $this->getLevel($levelId);
        $levelArray = explode(' ', $level->name);
        $levelId = $levelId + 1;
        $nextLevel = $this->getLevel($levelId);

        $pointNextLevel = $nextLevel->point - $latestPoint;

        $data = [
            'current_point' => $point,
            'current_level' => $levelArray[1],
            'point_next_level' => $pointNextLevel,
        ];
        
        return $data;
    }

    private function getLevel($levelId)
    {
        return \App\TaskLevelPoint::where('id', $levelId)->first();
    }

}
