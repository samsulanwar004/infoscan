<?php

namespace App\Http\Controllers\Api;

use App\Services\SnapService;
use Illuminate\Http\Request;

class SnapController extends BaseApiController
{
    private $snapType = [
        'images', 'audios', 'tags'
    ];

    public function store(Request $request)
    {
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
