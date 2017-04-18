<?php

namespace App\Http\Controllers\Api;

use App\Services\SnapService;
use App\Services\PointService;
use Illuminate\Http\Request;
use Exception;
use Validator;
use Carbon\Carbon;

class SnapController extends BaseApiController
{
    const SNAP_MODE_AUDIO = 'audios';

    private $handlerType = [
        'receipt',
        'generalTrade',
        'handWritten',
    ];

    private $date;

    public function __construct()
    {
        $this->date = Carbon::now('Asia/Jakarta');
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

            //get sanp count by daily and weekly
            $countDaily = $snapService->countMemberSnap($type, $mode, $date);
            $countWeekly = $snapService->countMemberSnap($type, $mode, $monday, $nextMonday);

            //get limit by task point
            $limits = (new PointService)->getLimitTaskPoint($type, $mode);

            $limit = [];
            foreach ($limits as $lim) {
                $limit[$lim->name] = $lim->limit;
            }

            //limit for daily
            if (isset($limit['daily']) && $countDaily >= $limit['daily'])
            {
                return $this->error('Snap Anda sudah mencapai batas harian', 400, true);
            }

            //limit for weekly
            if (isset($limit['weekly']) && $countWeekly >= $limit['weekly'])
            {
                return $this->error('Snap Anda sudah mencapai batas mingguan', 400, true);
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
                if (isset($limit['daily']) && $nextCountDaily >= $limit['daily'])
                {
                    $snap->sendSnapLimitNotification('daily', $mode = '');
                }

                //push notif limit for weekly
                if (isset($limit['weekly']) && $nextCountWeekly >= $limit['weekly'])
                {
                    $snap->sendSnapLimitNotification('weekly', $mode = '');
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
}
