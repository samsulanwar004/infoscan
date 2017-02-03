<?php

namespace App\Http\Controllers\Api;

use App\Services\SnapService;
use Illuminate\Http\Request;
use Exception;
use Validator;

class SnapController extends BaseApiController
{
    const SNAP_MODE_AUDIO = 'audio';

    private $handlerType = [
        'receipt',
        'generalTrade',
        'handWritten',
    ];

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
                return $this->error('There is no need [request_code] field anymore. Please [request_code] from request payloads.');
            }

            $validation = $this->validRequest($request);
            if ($validation->fails()) {
                return $this->error($validation->errors(), 400, true);
            }

            $type = $request->input('snap_type');
            $method = strtolower($type) . 'Handler';

            // add request code on the fly
            // TODO: need to refactor!!!!!

            $request->request->add([
                'request_code' => strtolower(str_random(10)),
            ]);
            (new SnapService)->{$method}($request);

            return $this->success();
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
            'snap_images' => 'array',
            'snap_images.*' => 'required|mimes:jpeg',
        ];

        // declare new rules depend on handler type (snap_type)
        $newRules = [];
        if ('generaltrade' === $snapType || 'handwritten' === $snapType) {
            $modeType = strtotime($request->input('mode_type'));

            if ($modeType === self::SNAP_MODE_AUDIO) {
                $newRules = [
                    'mode_type' => 'required',
                    'snap.audios.*' => 'required|mimes:mp3,raw',
                ];
            } else {
                $newRules = [
                    'mode_type' => 'required',
                    'snap.tags.*.name' => 'required',
                    'snap.tags.*.quantity' => 'present|numeric',
                    'snap.tags.*.total_price' => 'present|numeric',
                ];
            }
        }

        $rules = array_merge($rules, $newRules);

        return Validator::make($request->all(), $rules);
    }
}
