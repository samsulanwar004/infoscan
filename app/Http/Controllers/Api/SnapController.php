<?php

namespace App\Http\Controllers\Api;

use App\Services\SnapService;
use Illuminate\Http\Request;
use Exception;

class SnapController extends BaseApiController
{
    private $snapType = [
        'images', 'audios', 'tags'
    ];

    private $handlerType = [
        'receipt', 'general_trade', 'handwritten'
    ];

    public function store(Request $request)
    {
        // TODO: Refactor about snap type;
        // check whether the snap type = 'bill' or = 'handwritten' or = 'tags'.
        // make sure that all of inbound data has passed validation ( validation rules by snap type ).
        // if == 'bill' then use OCR.
        // if == 'handwritten' must check whether tags / audio and make sure image is uploaded.
        // if == 'product arrange' must check whether tags / audio and make sure image is uploaded
        try {
            $type = $request->input('snap_type');

            $method = strtolower($type) . 'Handler';
            $process = $this->{$method}($request);
        } catch (Exception $e) {
            dd($e->getMesssage());
        }
    }

    private function imagesHandler(Request $request)
    {
        $services = (new SnapService)->handle($request);
        dd($services);
    }

    private function audiosHandler(Request $request)
    {
        dd('masuk ke arrange');
    }
}
