<?php

namespace App\Services;

use App\Task;
use App\TaskLevelPoint;
use App\TaskLevelPoints;
use App\PromoPoint;
use App\PromoLevelPoint;
use App\SnapTag;
use App\LimitPoint;
use App\TaskPoint;
use App\BonusLevelPoint;
use App\BonusPoint;
use DB;
use Carbon\Carbon;
use App\Transformers\PortalPointTransformer;
use App\Exchange;
use App\CityRate;
use App\TaskLimit;
use App\Jobs\PointProcessJob;

class PointService
{

    const CACHE_NAME       = 'point.pivot';
    const CACHE_PROMO_NAME = 'promo.point.pivot';
    const CACHE_TASK_LIMIT = 'point.limit';
    const CACHE_BONUS_NAME = 'bonus.point';

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
        if ($pivots = cache(self::CACHE_NAME)) {
            return $pivots;
        }

        $points = DB::select('select t.id, t.name as task_name, l.name as level_name, tlp.point from tasks_level_points as tlp
inner join tasks as t on t.id = tlp.task_id
inner join level_points as l on l.id = tlp.level_id order by t.id;');
        $result = [];
        foreach ($points as $pivot) {
            $result[] = [
                'Task' => $pivot->id . ' ' . $pivot->task_name,
                'Level' => $pivot->level_name,
                'Point' => $pivot->point,
            ];
        }

        cache()->put(self::CACHE_NAME, $result, 1440);

        return $result;
    }

    public function getPromoPivotGrid()
    {
        if ($pivots = cache(self::CACHE_PROMO_NAME)) {
            return $pivots;
        }

        $points = DB::select('select c.id, c.city_name as city_name, c.is_active, l.name as level_name, plp.point from promo_level_points as plp
inner join promo_points as c on c.id = plp.promo_point_id
inner join level_points as l on l.id = plp.level_id;');
        $result = [];
        foreach ($points as $pivot) {
            $active = ($pivot->is_active == 0) ? '(Deactive)' : '';
            $result[] = [
                'City' => $pivot->id . ' ' . $pivot->city_name . ' ' . $active,
                'Level' => $pivot->level_name,
                'Point' => $pivot->point,
            ];
        }

        cache()->put(self::CACHE_PROMO_NAME, $result, 1440);

        return $result;
    }

    public function getBonusPivotGrid()
    {
        if ($pivots = cache(self::CACHE_BONUS_NAME)) {
            return $pivots;
        }

        $points = DB::select('select b.id, b.bonus_name, b.is_active, l.name as level_name, blp.point from bonus_level_points as blp
inner join bonus_points as b on b.id = blp.bonus_point_id
inner join level_points as l on l.id = blp.level_id;');
        $result = [];
        foreach ($points as $pivot) {
            $active = ($pivot->is_active == 0) ? '(Deactive)' : '';
            $result[] = [
                'Bonus' => $pivot->id . ' ' . $pivot->bonus_name . ' ' . $active,
                'Level' => $pivot->level_name,
                'Point' => $pivot->point,
            ];
        }

        cache()->put(self::CACHE_BONUS_NAME, $result, 1440);

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

        cache()->forget(self::CACHE_TASK_LIMIT);

        cache()->forget(self::CACHE_BONUS_NAME);

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

        $name = $request['name'];
        $date = $request['start_at'];
        $dateArray = explode(' - ', $date);

        $promos = [];
        for ($i = 0; $i < count($name); $i++) {
            $promo = new PromoPoint;
            $promo->city_name = $request['name'][$i];
            $promo->point_city = $request['point_city'];
            $promo->start_at = $dateArray[0];
            $promo->end_at = $dateArray[1];

            $promo->save();

            $promos[] = $promo->id;
        }

        return $promos;
    }

    public function addBonusPoint($request)
    {

        $bonus = new BonusPoint;
        $bonus->bonus_name = $request['bonus_name'];

        $bonus->save();

        return $bonus;
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
        $name = $request->input('name');
        $date = $request->input('start_at');
        $dateArray = explode(' - ', $date);

        $promos = [];
        for ($i = 0; $i < count($name); $i++) {
            $promo = $this->getPromoPointById($id);

            if ($promo->city_name == $name[$i]) {
                $promo->city_name = $name[$i];
                $promo->point_city = $request->input('point_city');
                $promo->start_at = $dateArray[0];
                $promo->end_at = $dateArray[1];
                $promo->is_active = $request->has('is_active') ? 1 : 0;

                $promo->update();
            } else {
                $promo = new PromoPoint;
                $promo->city_name = $name[$i];
                $promo->point_city = $request->input('point_city');
                $promo->start_at = $dateArray[0];
                $promo->end_at = $dateArray[1];
                $promo->is_active = $request->has('is_active') ? 1 : 0;

                $promo->save();
            }
            $promos[] = $promo->id;
        }

        return $promos;
    }

    public function updateBonusPoint($request, $id)
    {
        $name = $request->input('bonus_name');
        $bonus = $this->getBonusPointById($id);
        $bonus->bonus_name = $name;
        $bonus->is_active = $request->has('is_active') ? 1 : 0;

        $bonus->update();

        return $bonus;
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

                if ($taskLevelPoint == false) {
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

            foreach ($request->input('limit') as $limitName => $limit) {
                $taskLimitPoint = $this->getTaskLimitPoints($task->id, $limitName);

                if ($taskLimitPoint == false) {
                    $newtaskLimitPoint = new LimitPoint;
                    $newtaskLimitPoint->name = $limitName;
                    $newtaskLimitPoint->limit = $limit;
                    $newtaskLimitPoint->task()->associate($task);
                    $newtaskLimitPoint->save();
                } else {
                    $taskLimitPoint->name = $limitName;
                    $taskLimitPoint->limit = $limit;
                    $taskLimitPoint->update();
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

    public function getTaskLimitPoints($taskid, $name)
    {
        return LimitPoint::where('task_id', $taskid)
                         ->where('name', $name)
                         ->first();
    }

    public function getPromoLevelPoints($promoPointid, $levelid)
    {
        $plp = PromoLevelPoint::where('promo_point_id', '=', $promoPointid)
                              ->where('level_id', '=', $levelid)
                              ->first();

        return $plp;
    }

    public function getBonusLevelPoints($bonusPointid, $levelid)
    {
        $blp = BonusLevelPoint::where('bonus_point_id', '=', $bonusPointid)
                              ->where('level_id', '=', $levelid)
                              ->first();

        return $blp;
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
        if ($level = $this->getLevelByName($levelName)) {
            return $level;
        } else {
            $name = 'Level ' . $levelName;
            $level = $this->addLevel($name);

            return $level;
        }
    }

    public function getTaskById($id)
    {
        $t = Task::with('point')
                 ->with('limit')
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

    public function getBonusPointById($id)
    {
        $t = BonusPoint::with('levels')
                       ->where('id', '=', $id)
                       ->first();

        return $t;
    }

    public function addPromoLevelPoint($request)
    {
        DB::beginTransaction();

        $promo = $this->addCityPromo($request->all());

        if ($promo) {
            for ($i = 0; $i < count($promo); $i++) {
                foreach ($request->input('levels') as $levelName => $point) {
                    $promoLevelPoint = new PromoLevelPoint;
                    $promoLevelPoint->point = $point;
                    $level = $this->findLevel($levelName);
                    $promoLevelPoint->levelPoint()->associate($level);
                    $promoLevelPoint->promoPoint()->associate($promo[$i]);
                    $promoLevelPoint->save();
                }
            }

            $this->removeCache();

            DB::commit();

            return true;
        }

        DB::rollBack();

        return false;
    }

    public function addBonusLevelPoint($request)
    {
        DB::beginTransaction();

        $bonus = $this->addBonusPoint($request->all());

        if ($bonus) {
            foreach ($request->input('levels') as $levelName => $point) {
                $bonusLevelPoint = new BonusLevelPoint;
                $bonusLevelPoint->point = $point;
                $level = $this->findLevel($levelName);
                $bonusLevelPoint->levelPoint()->associate($level);
                $bonusLevelPoint->bonusPoint()->associate($bonus);
                $bonusLevelPoint->save();
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
            for ($i = 0; $i < count($promo); $i++) {
                $levelId = [];
                foreach ($request->input('levels') as $levelName => $point) {
                    $level = $this->findLevel($levelName);
                    $levelId[] = $level->id;
                    $promoLevelPoint = $this->getPromoLevelPoints($promo[$i], $level->id);

                    if ($promoLevelPoint == false) {
                        $newPromoLevelPoint = new PromoLevelPoint;
                        $newPromoLevelPoint->point = $point;
                        $newPromoLevelPoint->levelPoint()->associate($level);
                        $newPromoLevelPoint->promoPoint()->associate($promo[$i]);
                        $newPromoLevelPoint->save();
                    } else {
                        $promoLevelPoint->point = $point;
                        $promoLevelPoint->update();
                    }

                }
                // delete unnesesary level
                PromoLevelPoint::where('promo_point_id', '=', $id)
                               ->whereNotIn('level_id', $levelId)->delete();
            }

            $this->removeCache();

            DB::commit();

            return true;
        }

        DB::rollBack();

        return false;
    }

    public function updateBonusLevelPoint($request, $id)
    {
        DB::beginTransaction();

        $bonus = $this->updateBonusPoint($request, $id);

        if ($bonus) {
            $levelId = [];
            foreach ($request->input('levels') as $levelName => $point) {
                $level = $this->findLevel($levelName);
                $levelId[] = $level->id;
                $bonusLevelPoint = $this->getBonusLevelPoints($bonus->id, $level->id);

                if ($bonusLevelPoint == false) {
                    $newBonusLevelPoint = new BonusLevelPoint;
                    $newBonusLevelPoint->point = $point;
                    $newBonusLevelPoint->levelPoint()->associate($level);
                    $newBonusLevelPoint->bonusPoint()->associate($bonus);
                    $newBonusLevelPoint->save();
                } else {
                    $bonusLevelPoint->point = $point;
                    $bonusLevelPoint->update();
                }

            }
            // delete unnesesary level
            BonusLevelPoint::where('bonus_point_id', '=', $id)
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
     *
     * @param integer $memberId
     * @param string  $type
     * @param string  $mode
     * @param integer $tags
     *
     * @return integer point
     */
    public function calculateEstimatedPoint($memberId, $type, $mode, $tags)
    {

        $type = $this->getTypeId($type);
        $mode = $this->getModeId($mode);

        $code = $type . $mode;

        $levelId = (new MemberService)->getLevelIdByMemberId($memberId);

        $point = $this->getPointByCode($code, $levelId);

        if ($point == null && $levelId > 1) {
            $point = $this->getPointMaxLevelByCode($code);
        }

        $data = [
            'percent' => isset($point) ? $point->percent : 0,
            'point' => isset($point) ? $point->point : 0,
        ];

        return $data;
    }

    /**
     * Get calculate Promo point
     *
     * @param integer $memberId
     * @param string  $city
     *
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
        $memberAdd = $tags['member_add'];
        $crowdSourceEdit = $tags['crowdsource_edit'];
        $crowdSourceAdd = $tags['crowdsource_add'];

        $calculateTask = $this->calculateEstimatedPoint($memberId, $type, $mode, $memberTag);

        $promo = $this->calculatePromoPoint($memberId, $city);

        $pointPromo = $promo['point_city'] + $promo['point_level_city'];

        $totalTag = $memberAdd + $crowdSourceEdit + $crowdSourceAdd;

        if ($type == 'receipt') {
            $point = $calculateTask['point'] * count($files);
            $point = $point + $calculatePromo['point_city'] + $calculatePromo['point_level_city'];
        } else {
            if ($mode == 'no_mode' || $mode == 'audios') {
                $files = $files->filter(function ($value, $Key) {
                    return starts_with($value->file_mimes, 'image');
                });

                $point = $calculateTask['point'] * count($files);
                $point = $point + $calculatePromo['point_city'] + $calculatePromo['point_level_city'];
            } else {
                //new logic for snap member snap * task point
                if ($memberAdd <= 0) {
                    $fixedPoint = $calculateTask['percent'];
                    $point = $fixedPoint + $calculatePromo['point_city'] + $calculatePromo['point_level_city'];
                } else {
                    $fixedPoint = $memberAdd * $calculateTask['point'];
                    $point = $fixedPoint + $calculatePromo['point_city'] + $calculatePromo['point_level_city'];
                }
            }
        }

        $totalPoint = round($point);

        return [
            'point' => $point,
            'promo' => $pointPromo,
        ];
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

    public function getPortalPoint($member)
    {
        $transaction = (new TransactionService);
        $point = $transaction->getCreditMember($member->member_code);

        $snaps = (new SnapService)->getSnapByMemberCode($member->member_code);

        $historys = $transaction->getHistoryMember($member->id);

        $historys = $historys->filter(function ($value, $Key) {
            return $value->group == 'portalpoint';
        });

        $end = $this->date->format('Y-m-d');
        $start = $this->date->subWeek()->format('Y-m-d');

        $historys = $historys->filter(function ($value, $Key) use ($start, $end) {
            return $value->created_at->format('Y-m-d') >= $start &&
                $value->created_at->format('Y-m-d') <= $end;
        });

        $notif = [];
        foreach ($historys as $history) {
            $notif[] = [
                'title' => $history->content['title'],
                'description' => $history->content['description'],
                'flag' => isset($history->content['flag']) ? $history->content['flag'] : 1,
                'date' => $history->created_at->toDateTimeString(),
            ];
        }

        $snaps = $snaps->filter(function ($value, $Key) {
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
            'history' => $notif,
        ];

        return $data;
    }

    public function getDetailMe($member)
    {
        $point = (new TransactionService)->getCreditMember($member->member_code);
        $cash = (new TransactionService)->getCashCreditMember($member->member_code);
        $memberService = (new MemberService)->getLevelByMemberId($member->id);
        $snaps = (new SnapService)->getSnapByMemberId($member->id);
        $levelId = $memberService['level_id'];
        $latestPoint = $memberService['latest_point'];
        $level = $this->getLevel($levelId);
        $levelArray = explode(' ', $level->name);
        $levelId = $levelId + 1;
        $nextLevel = $this->getLevel($levelId);
        $nextPoint = ($nextLevel == null) ? $latestPoint : $nextLevel->point;
        $pointNextLevel = $nextPoint - $latestPoint;

        //get latest member point
        $score = $memberService['latest_point'];

        if ($member->leaderboard_score != $score) {
            $member->leaderboard_score = $score;

            $member->update();
        }

        if ($member->temporary_point != $point || $member->temporary_level != $levelArray[1]) {
            $member->temporary_point = $point;
            $member->temporary_level = $levelArray[1];

            $member->update();
        }

        $ml = (new MemberService)->getLatestMemberLevelById($member->id);
        $lastLevel = $ml ? $ml->level_id : 1;
        if ($memberService['level_id'] > $lastLevel) {
            //save to db member levels
            $ml = new \App\MemberLevel;
            $ml->latest_point = $point;
            $ml->latest_level_point = $levelArray[1];
            $ml->member()->associate($member);
            $ml->level()->associate($memberService['level_id']);
            $ml->save();

            //get bonus level
            $bonus = DB::table('bonus_points')
                       ->join('bonus_level_points', 'bonus_points.id', '=', 'bonus_level_points.bonus_point_id')
                       ->select('bonus_level_points.point')
                       ->orderBy('bonus_points.id', 'DESC')
                       ->where('bonus_level_points.level_id', $memberService['level_id'])
                       ->first();

            if ($bonus) {
                //queue for point process
                $config = config('common.queue_list.point_process');
                $type = config('common.transaction.transaction_type.bonus');
                $job = (new PointProcessJob($member->member_code, $bonus->point, $type))
                    ->onQueue($config)
                    ->onConnection(env('INFOSCAN_QUEUE'));
                dispatch($job);
            }
        }

        $estimated = [];
        foreach ($snaps as $snap) {
            $estimated[] = $snap->estimated_point;
        }

        $estimated = collect($estimated)->sum();

        $data = [
            'current_point' => $point,
            'current_cash' => $cash,
            'current_level' => $levelArray[1],
            'point_next_level' => $pointNextLevel,
            'estimated_point' => $estimated,
        ];

        return $data;
    }

    public function getLevel($levelId)
    {
        return \App\TaskLevelPoint::where('id', $levelId)->first();
    }

    private function getPointByCode($code, $levelId)
    {
        return \DB::table('tasks')
                  ->join('tasks_level_points', 'tasks.id', '=', 'tasks_level_points.task_id')
                  ->join('level_points', 'level_points.id', '=', 'tasks_level_points.level_id')
                  ->select('tasks.percentage as percent', 'tasks_level_points.point as point')
                  ->where('tasks.code', $code)
                  ->where('tasks_level_points.level_id', $levelId)
                  ->first();
    }

    private function getPointMaxLevelByCode($code)
    {
        return \DB::table('tasks')
                  ->join('tasks_level_points', 'tasks.id', '=', 'tasks_level_points.task_id')
                  ->join('level_points', 'level_points.id', '=', 'tasks_level_points.level_id')
                  ->select('tasks.percentage as percent', 'tasks_level_points.point as point')
                  ->where('tasks.code', $code)
                  ->orderBy('tasks_level_points.point', 'DESC')
                  ->first();
    }

    public function getTaskPivotLimit()
    {
        if ($pivots = cache(self::CACHE_TASK_LIMIT)) {
            return $pivots;
        }

        $points = DB::select('select id, task_category as name, limit_daily as daily, limit_weekly as weekly
            from task_limits order by id;');
        $result = [];
        foreach ($points as $pivot) {
            $result[] = [
                'Task' => $pivot->id . ' ' . $pivot->name,
                'limit_name' => 'Daily',
                'Limit' => $pivot->daily,
            ];
            $result[] = [
                'Task' => $pivot->id . ' ' . $pivot->name,
                'limit_name' => 'Weekly',
                'Limit' => $pivot->weekly,
            ];
        }

        cache()->put(self::CACHE_TASK_LIMIT, $result, 1440);

        return $result;
    }

    public function getLimitTaskPoint($type, $mode)
    {
        $code = $this->getTypeId($type);
        // $mode = $this->getModeId($mode);

        // $code = $type.$mode;

        return DB::table('tasks')
                 ->join('limit_points', 'limit_points.task_id', '=', 'tasks.id')
                 ->where('tasks.code', 'like', $code . '%')
                 ->select('limit_points.name', 'limit_points.limit')
                 ->get();
    }

    public function getTaskLimitByName($name)
    {
        $category = ($name == 'generalTrade') ? 'general trade' : strtolower($name);
        return TaskLimit::where('task_category', $category)
            ->first();
    }

    public function addTaskPoint($request)
    {

        if ($request->input('task_type') == 'a') {
            $data = [
                'name' => $request->input('name'),
                'code' => sprintf("%s|%s|%s", $request->input('task_type'), $request->input('range_start'), $request->input('range_end')),
            ];
            DB::beginTransaction();
            $task = $this->addNewTask($data);

            if ($task) {
                $point = new TaskPoint;
                $point->range_start = $request->input('range_start');
                $point->range_end = $request->input('range_end');
                $point->point = $request->input('point');

                $point->task()->associate($task);
                $point->save();

                $this->removeCache();

                DB::commit();

                return true;
            }

            DB::rollBack();

            return false;
        } else {
            $data = [
                'name' => $request->input('name'),
                'code' => sprintf("%s|%s", $request->input('task_type'), $request->input('task_mode')),
            ];
            DB::beginTransaction();
            $task = $this->addNewTask($data);

            if ($task) {
                $point = new TaskPoint;
                $point->point = $request->input('point');

                $point->task()->associate($task);
                $point->save();

                $this->removeCache();

                DB::commit();

                return true;
            }

            DB::rollBack();

            return false;
        }

    }

    public function addNewTask($data)
    {
        $task = new Task;
        $task->name = $data['name'];
        $task->code = $data['code'];
        $task->save();

        return $task;
    }

    public function getNewPivotGrid()
    {
        if ($pivots = cache(self::CACHE_NAME)) {
            return $pivots;
        }

        $points = DB::select('select t.id, t.name as task_name, tp.point from task_points as tp
inner join tasks as t on t.id = tp.task_id order by t.id;');
        $result = [];
        foreach ($points as $pivot) {
            $result[] = [
                'Task' => $pivot->id . ' ' . $pivot->task_name,
                'Level' => 'Points',
                'Point' => $pivot->point,
            ];
        }

        cache()->put(self::CACHE_NAME, $result, 1440);

        return $result;
    }

    public function updateTaskPoint($request, $id)
    {
        if ($request->input('task_type') == 'a') {
            $data = [
                'name' => $request->input('name'),
                'code' => sprintf("%s|%s|%s", $request->input('task_type'), $request->input('range_start'), $request->input('range_end')),
            ];
            DB::beginTransaction();
            $task = $this->updateNewTask($data, $id);

            if ($task) {
                $point = TaskPoint::where('task_id', $task->id)->first();
                $point->range_start = $request->input('range_start');
                $point->range_end = $request->input('range_end');
                $point->point = $request->input('point');

                $point->update();

                $this->removeCache();

                DB::commit();

                return true;
            }

            DB::rollBack();

            return false;
        } else {
            $data = [
                'name' => $request->input('name'),
                'code' => sprintf("%s|%s", $request->input('task_type'), $request->input('task_mode')),
            ];
            DB::beginTransaction();
            $task = $this->updateNewTask($data, $id);

            if ($task) {
                $point = TaskPoint::where('task_id', $task->id)->first();
                $point->point = $request->input('point');
                $point->update();

                $this->removeCache();

                DB::commit();

                return true;
            }

            DB::rollBack();

            return false;
        }
    }

    public function updateNewTask($data, $id)
    {
        $task = $this->getTaskById($id);
        $task->name = $data['name'];
        $task->code = $data['code'];
        $task->update();

        return $task;
    }

    public function calculateNewApprovePoint($snaps)
    {
        $memberId = $snaps->member_id;
        $type = $snaps->snap_type;
        $mode = $snaps->mode_type;
        $city = $snaps->outlet_city;
        $files = $snaps->files;

        $tags = (new SnapService)->getCountOfTags($files);
        $memberAdd = $tags['member_add'];
        $crowdSourceEdit = $tags['crowdsource_edit'];
        $crowdSourceAdd = $tags['crowdsource_add'];

        if ($memberAdd > 0 && $crowdSourceAdd > 0) {
            $commonId = 2;
        } elseif ($crowdSourceEdit > 0) {
            $commonId = 2;
        } elseif ($memberAdd > 0) {
            $commonId = 3;
        } else {
            $commonId = 1;
        }

        // get code task
        $code = $this->getCodeTaskTag($type, $mode, $commonId);

        //save code in file
        $file = \App\SnapFile::where('id', $files[0]->id)->first();
        $file->image_code = $code;
        $file->update();

        $task = (new SnapService)->getTaskPointByCode($code);

        $point = isset($task->point) ? $task->point : 0;

        return $point;
    }

    private function getCodeTaskTag($type, $mode, $commonId)
    {
        if ($type == 'handWritten') {
            $type = 'b';
        } else {
            $type = 'c';
        }

        switch ($mode) {
            case 'tags':
                $mode = $type . '|' . $commonId;
            break;

            default:
                $mode = $type . '|' . $commonId;
            break;
        }

        return $mode;
    }

    public function getCurrencyRate()
    {
        return Exchange::orderBy('created_at', 'DESC')
                       ->first();
    }

    public function getCurrencyRateByCity($city)
    {
        return CityRate::where('city_name', strtolower($city))
                       ->where('is_active', 1)
                       ->orderBy('created_at', 'DESC')
                       ->first();
    }

    public function persistTaskLimit($request, $id = null)
    {
        $limit = is_null($id) ? new TaskLimit : $this->getTaskLimitById($id);
        $limit->task_category = $request->input('name');
        $limit->limit_daily = $request->input('limit_daily');
        $limit->limit_weekly = $request->input('limit_weekly');
        $limit->save();

        $this->removeCache();
    }

    public function getTaskLimitById($id)
    {
        return TaskLimit::where('id', $id)
            ->first();
    }

}
