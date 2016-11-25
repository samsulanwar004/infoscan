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

    const TAGS_FIELD_NAME = 'snap_tags';

    const IMAGE_TYPE_NAME = 'images';

    const AUDIO_TYPE_NAME = 'audios';

    const DEFAULT_FILE_DRIVER = 's3';

    /**
     * @param \Illuminate\Http\Request $request
     * @return array|mixed
     * @throws \App\Exceptions\Services\SnapServiceException
     */
    public function receiptHandler(Request $request)
    {
        $images = $this->imagesProcess($request);
        if(0 === count($images)) {
            throw new SnapServiceException('There is no images was processed. Something wrong with the system!');
        }

        return $images;
    }

    /**
     * @param \Illuminate\Http\Request $request
     */
    public function generalTradeHandler(Request $request)
    {

    }

    /**
     * @param \Illuminate\Http\Request $request
     */
    public function handWrittenHandler(Request $request)
    {

    }

    /**
     * Store all image to local storage and upload the to s3 by event jobs (queue).
     *
     * @param  Request $request
     * @return array|mixed
     * @throws \App\Exceptions\Services\SnapServiceException
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

    /**
     * @param \Illuminate\Http\Request $request
     * @return array
     * @throws \App\Exceptions\Services\SnapServiceException
     */
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

    /**
     * @param \Illuminate\Http\Request $request
     */
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
        $i = 0;
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

                $fileList[$i]['filename'] = $filename;
                $fileList[$i]['file_size'] = $file->getSize();
                $fileList[$i]['file_mime'] = $file->getMimeType();

                ++$i;
            }
        }

        return $fileList;
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return int
     */
    private function countOfImage(Request $request)
    {
        $images = ($request->hasFile(self::IMAGE_FIELD_NAME) && null !== $request->file(self::IMAGE_FIELD_NAME)) ?
            $request->file(self::IMAGE_FIELD_NAME) :
            [];

        return count($images);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return int
     */
    private function countOfAudio(Request $request)
    {
        $audios = ($request->hasFile(self::AUDIO_FIELD_NAME) && null !== $request->file(self::AUDIO_FIELD_NAME)) ?
            $request->file(self::AUDIO_FIELD_NAME) :
            [];

        return count($audios);
    }

    /**
     * @param array $data
     */
    private function persistData(array $data)
    {

    }
}
