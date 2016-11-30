<?php

namespace App\Services;

use App\Exceptions\Services\SnapServiceException;
use App\Jobs\UploadToS3;
use Dusterio\PlainSqs\Jobs\DispatcherJob;
use DB;
use Illuminate\Http\Request;
use Storage;
use Exception;

class SnapService
{
    const IMAGE_FIELD_NAME = 'snap_images';

    const AUDIO_FIELD_NAME = 'snap_audios';

    const TAGS_FIELD_NAME = 'snap_tags';

    const IMAGE_TYPE_NAME = 'images';

    const AUDIO_TYPE_NAME = 'audios';

    const TAG_TYPE_NAME = 'tags';

    const DEFAULT_FILE_DRIVER = 's3';
    /**
     * @var string
     */
    private $s3Url;
    /**
     * @var bool
     */
    private $isNeedRecognition = true;

    public function __construct()
    {
        $this->s3Url = env('S3_URL', '');
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return array|mixed
     * @throws \App\Exceptions\Services\SnapServiceException
     */
    public function receiptHandler(Request $request)
    {
        $images = $this->imagesProcess($request);
        if (0 === count($images)) {
            throw new SnapServiceException('There is no images was processed. Something wrong with the system!');
        }

        // build data
        $data = [
            'request_code' => $request->input('request_code'),
            'snap_type' => 'receipt',
            'snap_mode' => 'images',
            'snap_files' => $images,
        ];

        if(! $this->presistData($request, $images)) {
            throw new Exception('Error when saving data in database!');
        }

        // send dispatcher
        $job = $this->getPlainDispatcher($data);
        dispatch($job);

        return $data;
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
     * @param  array $files
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
            if (true === Storage::disk(self::DEFAULT_FILE_DRIVER)
                                ->getDriver()
                                ->put($filename, file_get_contents($file->getPathName()), [
                                    'visibility' => 'public',
                                    'ContentType' => $file->getMimeType(),
                                ])
            ) {

                $fileList[$i]['file_code'] = str_random(10);
                $fileList[$i]['filename'] = $filename;
                $fileList[$i]['file_link'] = $this->completeImageLink($filename);
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
     * @param \Illuminate\Http\Request $request
     * @param array $files
     */
    public function presistData(Request $request, array $files)
    {
        DB::beginTransaction();
        try {
            $snap = $this->createSnap($request);
            $this->createFiles($request, $files, $snap);
        } catch (\Exception $e) {
            DB::rollback();

            logger($e);

            return false;
        }

        DB::commit();

        return true;
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return \App\Snap;
     */
    private function createSnap(Request $request)
    {
        $snap = new \App\Snap;
        $snap->request_code = $request->input('request_code');
        $snap->member_id = 1; // TODO: must get who is posting this data
        $snap->snap_type = $request->input('snap_type');
        if ($request->exists('mode_type') || 'receipt' !== strtolower($request->input('snap_type'))) {
            $snap->mode_type = $request->input('mode_type');
        }
        $snap->status = 'new';
        $snap->save();

        return $snap;
    }

    /**
     * @param $request
     * @param array $files
     * @param \App\Snap $snap
     * @return bool
     */
    private function createFiles($request, array $files, \App\Snap $snap)
    {
        if ($this->isMultidimensiArray($files)) {
            foreach ($files as $file) {
                $file = $this->persistFile($request, $file, $snap);

                $this->createTag($request, $file);
            }

            return true;
        }

        $this->persistFile($request, $files, $snap);

        return true;
    }

    /**
     * Insert a new files data into database.
     *
     * @param \Illuminate\Http\Request $request
     * @param array $data
     * @param $snap
     * @return \App\SnapFile
     */
    private function persistFile(Request $request, array $data, $snap)
    {
        $f = new \App\SnapFile();
        $f->file_path = $data['filename'];
        $f->file_code = $data['file_code'];
        $f->file_mimes = $data['file_mime'];
        $f->file_dimension = null;
        if ($this->hasMode($request)) {
            $f->mode_type = $request->input('mode_type');
        }

        $f->snap()->associate($snap);
        $f->save();

        return $f;
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\SnapFile $file
     * @return array|string
     */
    private function createTag(Request $request, \App\SnapFile $file)
    {
        if (!$this->hasMode($request) || !$this->isTagsMode($request)) {
            return [];
        }

        $tags = $request->input(self::TAGS_FIELD_NAME);
        foreach ($tags as $t) {
            $tag = new \App\SnapTag();
            $tag->name = $t['name'];
            $tag->total_price = $t['price'];
            $tag->quantity = $t['quantity'];
            $tag->file()->associate($file);

            $tag->save();
        }

        return $tags;
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return bool
     */
    private function hasMode(Request $request)
    {
        return $request->exists('mode_type') && $request->has('mode_type');
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return bool
     */
    private function isTagsMode(Request $request)
    {
        if ($this->hasMode($request)) {
            return strtolower(self::TAG_TYPE_NAME) === strtolower($request->input('mode_type')) ? true : false;
        }

        return false;
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return bool
     */
    private function isAudioMode(Request $request)
    {
        if ($this->hasMode($request)) {
            return strtolower(self::AUDIO_TYPE_NAME) === strtolower($request->input('mode_type')) ? true : false;
        }

        return false;
    }


    /**
     * @param \Illuminate\Http\Request $request
     * @return bool
     */
    private function isNeedRecognition(Request $request)
    {
        if ('generaltrade' === strtolower($request->snap_type) ||
            'handwritten' === strtolower($request->snap_type)
        ) {

            return true;
        }

        return true;
    }

    /**
     * @param $object
     * @return \Dusterio\PlainSqs\Jobs\DispatcherJob
     */
    protected function getPlainDispatcher($object)
    {
        return new DispatcherJob($object);
    }

    /**
     * @param $filename
     * @return string
     */
    protected function completeImageLink($filename)
    {
        return $this->s3Url . '' . $filename;
    }

    /**
     * @param $array
     * @return bool
     */
    private function isMultidimensiArray($array)
    {
        $rv = array_filter($array, 'is_array');
        if (count($rv) > 0) {
            return true;
        }

        return false;
    }
}
