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

    public function store(Request $request)
    {
        try {
            $validation = $this->validRequest($request);
            if ($validation->fails()) {
                return $this->error($validation->errors());
            }

            $type = $request->input('snap_type');
            $method = strtolower($type) . 'Handler';

            (new SnapService)->{$method}($request);

            return $this->success();
        } catch (Exception $e) {
            logger($e);

            return $this->error($e);
        }
    }

    private function validRequest(Request $request)
    {
        $snapType = strtolower($request->input('snap_type'));

        $rules = [
            'request_code' => 'required|size:10',
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
                    'snap.audios.*' => 'required|mimes:mp3',
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
