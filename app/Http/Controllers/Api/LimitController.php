<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Services\SnapService;
use App\Services\PointService;

class LimitController extends BaseApiController
{

    private $member;

    private $date;

    public function __construct()
    {
        $this->member = auth('api')->user();
        $this->date = Carbon::now('Asia/Jakarta');
    }

    public function index()
    {
        try {
            $limits = [];
            $limits[] = $this->isLimit('receipt');

            $limits[] = $this->isLimit('handWritten');

            $limits[] = $this->isLimit('generalTrade');

            return $this->success($limits, 200);
        } catch (\Exception $e) {
            return $this->error($e , 400, true);
        }
    }

    private function isLimit($type)
    {
        //create new date
        $date = $this->date->toDateString();
        $dateArr = explode('-', $date);
        $newDate = Carbon::create($dateArr[0], $dateArr[1], $dateArr[2]);
        $monday = $newDate->startOfWeek()->toDateString();
        $nextMonday = $newDate->startOfWeek()->addWeek()->toDateString();
        $snapService = (new SnapService);
        $member = $this->member;
        //get snap count by daily and weekly
        $countDaily = $snapService->countMemberSnap($member->id, $type, $date);
        $countWeekly = $snapService->countMemberSnap($member->id, $type, $monday, $nextMonday);

        //get limit by task point
        $limits = (new PointService)->getTaskLimitByName($type);

        if($limits) {
            $countDaily = $countDaily == $limits->limit_daily ? true : false;
            $countWeekly = $countWeekly == $limits->limit_weekly ? true : false;
        } else {
            $countDaily = true;
            $countWeekly = true;
        }

        $type = $this->getTypeSnap($limits->task_category);

        $limit = [
            'mode' => $type,
            'is_limit_daily' => $countDaily,
            'is_limit_weekly' => $countWeekly,
            'message_daily' => 'Limit foto '.$type.' kamu habis untuk hari ini! Coba lagi besok ya!',
            'message_weekly' => 'Limit foto '.$type.' kamu habis untuk minggu ini! Coba lagi minggu depan ya!',
        ];

        return $limit;
    }

    private function getTypeSnap($type)
    {
        switch ($type) {
            case 'Receipt':
                return 'Struk';
                break;
            case 'Handwritten':
                return 'Nota Tulis';
                break;
            case 'General Trade':
                return 'Warung';
                break;
            default:
                return 'No Mode';
                break;
        }
    }
}
