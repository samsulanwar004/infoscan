<?php

namespace App\Http\Controllers\Api;

use App\Services\SnapService;
use App\Services\PointService;
use Illuminate\Http\Request;
use Exception;
use Validator;
use Carbon\Carbon;
use App\Jobs\MemberActionJob;

class SnapController extends BaseApiController
{
    const SNAP_MODE_AUDIO = 'audios';

    private $handlerType = [
        'receipt',
        'generalTrade',
        'handWritten',
    ];

    private $date;

    private $member;

    public function __construct()
    {
        $this->date = Carbon::now('Asia/Jakarta');
        $this->member = auth('api')->user();
    }

    /**
     * Check snap status.
     *
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $memberId = $this->getActiveMember();
            $results = (new SnapService)->getMemberSnaps($memberId);

            return $this->success($results);
        } catch (Exception $e) {
            return $this->error($e, 400);
        }
    }

    /**
     * Process the snap data.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            if($request->has('request_code')) {
                return $this->error('There is no need [request_code] field anymore. Please remove [request_code] from request payloads.', 400, true);
            }

            $validation = $this->validRequest($request);
            if ($validation->fails()) {
                return $this->error($validation->errors(), 400, true);
            }

            $type = $request->input('snap_type');
            $mode = $request->input('mode_type');
            $method = strtolower($type) . 'Handler';

            //create new date
            $date = $this->date->toDateString();
            $dateArr = explode('-', $date);
            $newDate = Carbon::create($dateArr[0], $dateArr[1], $dateArr[2]);
            $monday = $newDate->startOfWeek()->toDateString();
            $nextMonday = $newDate->startOfWeek()->addWeek()->toDateString();
            $snapService = (new SnapService);
            $member = $this->getActiveMember();
            //get snap count by daily and weekly
            $countDaily = $snapService->countMemberSnap($member->id, $type, $date);
            $countWeekly = $snapService->countMemberSnap($member->id, $type, $monday, $nextMonday);

            // count for member tester
            if (in_array($member->email, config('common.user_tester'))) {
                $countDaily = 0;
                $countWeekly = 0;
            }

            //get limit by task point
            $limit = (new PointService)->getTaskLimitByName($type);

            //limit for daily
            if (isset($limit->limit_daily) && $countDaily >= $limit->limit_daily)
            {
                (new SnapService)->sendSnapLimitNotification();
                $type = $this->getTypeSnap($limit->task_category);
                return $this->error('Limit foto '.$type.' kamu habis untuk hari ini! Coba lagi besok ya!', 400, true);
            }

            //limit for weekly
            if (isset($limit->limit_weekly) && $countWeekly >= $limit->limit_weekly)
            {
                (new SnapService)->sendSnapLimitNotification();
                $type = $this->getTypeSnap($limit->task_category);
                return $this->error('Limit foto '.$type.' kamu habis untuk minggu ini! Coba lagi minggu depan ya!', 400, true);
            }

            // add request code on the fly
            // TODO: need to refactor!!!!!

            $request->request->add([
                'request_code' => strtolower(str_random(10)),
            ]);
            $snap = (new SnapService);
            $process = $snap->{$method}($request);

            if ($process) {
                $nextCountDaily = $countDaily + 1;
                $nextCountWeekly = $countWeekly + 1;
                //push notif limit for daily
                if (isset($limit->limit_daily) && $nextCountDaily >= $limit->limit_daily)
                {
                    $type = $this->getTypeSnap($limit->task_category);
                    //build data for member history
                    $memberId = $this->member->id;
                    $content = [
                        'type' => 'snap',
                        'title' => 'Limit',
                        'description' => 'Limit foto '.$type.' kamu habis untuk hari ini! Coba lagi besok ya!',
                    ];

                    $config = config('common.queue_list.member_action_log');
                    $job = (new MemberActionJob($memberId, 'notification', $content))->onQueue($config)->onConnection(env('INFOSCAN_QUEUE'));
                    dispatch($job);
                }

                //push notif limit for weekly
                if (isset($limit->limit_weekly) && $nextCountWeekly >= $limit->limit_weekly)
                {
                    $type = $this->getTypeSnap($limit->task_category);
                    //build data for member history
                    $memberId = $this->member->id;
                    $content = [
                        'type' => 'snap',
                        'title' => 'Limit',
                        'description' => 'Limit foto '.$type.' kamu habis untuk minggu ini! Coba lagi minggu depan ya!',
                    ];

                    $config = config('common.queue_list.member_action_log');
                    $job = (new MemberActionJob($memberId, 'notification', $content))->onQueue($config)->onConnection(env('INFOSCAN_QUEUE'));
                    dispatch($job);
                }

            }

            return $this->success([
                'data' => [
                    'request_code' => $process['request_code'],
                    'estimated_point' => $snap->getEstimatedPoint(),
                ]
            ]);
        } catch (Exception $e) {
            logger($e);

            return $this->error($e, 400);
        }
    }

    /**
     * Validate the user request input data.
     *
     * @param  Request $request
     * @return \Illuminate\Validation\Validator
     */
    private function validRequest(Request $request)
    {
        $snapType = strtolower($request->input('snap_type'));

        $rules = [
            //'request_code' => 'required|size:10|unique:snaps,request_code',
            'snap_type' => 'required|in:receipt,generalTrade,handWritten',
            'snap_images.*' => 'required|image',
            'mode_type' => 'required|in:input,tags,audios,image,no_mode'
        ];

        // declare new rules depend on handler type (snap_type)
        $newRules = [];
        if ('generaltrade' === $snapType || 'handwritten' === $snapType) {
            $modeType = strtolower($request->input('mode_type'));

            if ($modeType === self::SNAP_MODE_AUDIO) {
                $newRules = [
                    'mode_type' => 'required',
                    'snap.audios.*' => 'required|mimes:mp3,raw,flac',
                ];
            } else {
                $newRules = [
                    'mode_type' => 'required',
                    'snap.tags.*.name' => 'required',
                    // 'snap.tags.*.weight' => 'required',
                    'snap.tags.*.quantity' => 'present|numeric',
                    'snap.tags.*.total_price' => 'present|numeric',
                ];
            }
        }

        $rules = array_merge($rules, $newRules);

        return Validator::make($request->all(), $rules);
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
