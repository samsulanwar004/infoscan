<?php

namespace App\Services;

use App\Exceptions\Services\SnapServiceException;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

class SnapService
{

    const IMAGE_FIELD_NAME = 'snap_images';

    public function handle(Request $request, $type)
    {

    }

    protected function imageProcess(Request $request)
    {
        if ($this->countOfImage($request) <= 1) {
            throw new SnapServiceException('Can not do the process');
        }

        foreach ($request->file(self::IMAGE_FIELD_NAME) as  $image) {

        }
    }

    protected function audioProcess(Request $request)
    {

    }

    protected function tagProcess(Request $request)
    {

    }

    private function countOfImage(Request $request)
    {
        $images = ($request->has(self::IMAGE_FIELD_NAME) && null !== $request->file(self::IMAGE_FIELD_NAME)) ?
            $request->file(self::IMAGE_FIELD_NAME) :
            [];

        return count($images);
    }

    private function uploadProcess(UploadedFile $image)
    {

    }

}
