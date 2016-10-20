<?php

namespace App\Http\Controllers\Web;

use App\Services\ImageDetection;
use Illuminate\Http\Request;

class AssetController extends AdminController
{
    public function transferImages(Request $request)
    {
        $ocr = (new ImageDetection)->handle($request); die;
    }
}