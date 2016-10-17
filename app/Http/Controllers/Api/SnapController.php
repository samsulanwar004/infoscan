<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

class SnapController extends Controller
{
    public function store(Request $request)
    {
        switch ($action) {
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

    }

    private function arrange(Request $request)
    {

    }
}
