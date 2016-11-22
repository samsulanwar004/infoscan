<?php

namespace App\Services;

use App\Exceptions\Services\SnapServiceException;
use App\Jobs\UploadToS3;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Storage;

class SnapService
{

    const IMAGE_FIELD_NAME = 'snap_images';
    const AUDIO_FIELD_NAME = 'snap_audios';
    const IMAGE_TYPE_NAME = 'images';
    const AUDIO_TYPE_NAME = 'audios';
    const DEFAULT_FILE_DRIVER = 's3';

    public function handle(Request $request)
    {
        $type = $request->input('snap_type');
        $method = strtolower($type) . 'Process';

        $data = $this->{$method}($request);

        // 1. Save all data into database.
        // 2. Dispatch the OCR, audio, etc events.
    }

    /**
     * Store all image to local storage and upload the to s3 by event jobs (queue).
     *
     * @param  Request $request
     * @return mixed|array
     */
    protected function imagesProcess(Request $request)
    {
        if ($this->countOfImage($request) < 1) {
            throw new SnapServiceException('There is no images to process!');
        }

        $files = $request->file(self::IMAGE_FIELD_NAME);
        $imageList = $this->filesUploads($files, self::IMAGE_TYPE_NAME);

        if (empty($imageList)) {
            throw new SnapServiceException('There is no image to process!');
        }

        return $imageList;
    }

    protected function audiosProcess(Request $request)
    {
        if ($this->countOfAudio($request) < 1) {
            throw new SnapServiceException('There is no audios to process!');
        }

        $files = $request->file(self::AUDIO_FIELD_NAME);
        $audioList = $this->filesUploads($files, self::AUDIO_TYPE_NAME);

        if (empty($audioList)) {
            throw new SnapServiceException('There is no image to process!');
        }

        return $audioList;
    }

    protected function tagsProcess(Request $request)
    {

    }

    /**
     * Upload file to s3.
     *
     * @param  array  $files
     * @param  string $type
     * @return array
     */
    private function filesUploads(array $files, $type)
    {
        $fileList = [];
        foreach ($files as $file) {
            // format: "folderOnS3(type)/type-date-randomString.extension"
            $filename = sprintf(
                "%s/%s-%s-%s.%s",
                $type,
                $type,
                date('YmdHis'),
                strtolower(str_random(10)),
                $file->getClientOriginalExtension()
            );

            // validate that file successfully uploaded to s3
            if(true === Storage::disk(self::DEFAULT_FILE_DRIVER)
                                    ->getDriver()
                                    ->put($filename, file_get_contents($file->getPathName()), [
                                            'visibility' => 'public',
                                            'ContentType' => $file->getMimeType()
                                        ])){

                $fileList[] = $filename;
            }
        }

        return $fileList;
    }

    private function countOfImage(Request $request)
    {
        $images = ($request->hasFile(self::IMAGE_FIELD_NAME) && null !== $request->file(self::IMAGE_FIELD_NAME)) ?
            $request->file(self::IMAGE_FIELD_NAME) :
            [];

        return count($images);
    }

    private function countOfAudio(Request $request)
    {
        $audios = ($request->hasFile(self::AUDIO_FIELD_NAME) && null !== $request->file(self::AUDIO_FIELD_NAME)) ?
            $request->file(self::AUDIO_FIELD_NAME) :
            [];

        return count($audios);
    }
}
