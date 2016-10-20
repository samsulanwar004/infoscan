<?php

namespace App\Services;

use App\Libraries\GoogleVision;
use Illuminate\Http\Request;

class ImageDetection
{

    const IMAGE_KEY_NAME = 'images';

    public function handle(Request $request)
    {
        $images = $request->file(self::IMAGE_KEY_NAME);
        if (!is_array($images)) {
            $images = [$images];
        }

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