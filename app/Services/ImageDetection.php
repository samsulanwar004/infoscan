<?php

namespace App\Services;

use App\Libraries\GoogleVision;
use Illuminate\Http\Request;

class ImageDetection
{

    const IMAGE_KEY_NAME = 'images';

    public function handle(Request $request)
    {
        $images = $request->input(self::IMAGE_KEY_NAME);
        $key = config('services.google.vision.key');

        $ocrProcess = (new GoogleVision($key))->setImages($images);

        return $ocrProcess->handle();
    }

    protected function saveToDb()
    {
    }

    protected function uploadImages(Request $request)
    {

    }
}