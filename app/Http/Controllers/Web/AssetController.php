<?php

namespace App\Http\Controllers\Web;

use App\Services\ImageDetection;
use Request;

class AssetController extends AdminController
{
    public function transferImages(Request $request)
    {
        $ocr = (new ImageDetection)->handle($request);
        die;
    }
}