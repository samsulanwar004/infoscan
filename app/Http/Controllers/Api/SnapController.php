<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

class SnapController extends BaseApiController
{
    public function store(Request $request, $type = 'bill')
    {
        switch ($type) {
            case 'arrange':
                return $this->arrange($request);
                break;

            case 'bill':
            default:
                return $this->bill($request);
                break;
        }
    }

    private function bill(Request $request)
    {
        dd('masuk ke bill');
    }

    private function arrange(Request $request)
    {
        dd('masuk ke arrange');
    }
}
