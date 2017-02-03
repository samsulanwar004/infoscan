<?php

namespace App\Services;

use App\Exceptions\Services\SnapServiceException;
use App\Jobs\UploadToS3;
use App\Snap;
use App\SnapFile;
use App\SnapTag;
use DB;
use Dusterio\PlainSqs\Jobs\DispatcherJob;
use Exception;
use Illuminate\Http\Request;
use Storage;
use App\Libraries\GoogleMap;

class SnapService
{
    const IMAGE_FIELD_NAME = 'snap_images';

    const AUDIO_FIELD_NAME = 'snap_audios';

    const TAGS_FIELD_NAME = 'snap_tags';

    const IMAGE_TYPE_NAME = 'images';

    const AUDIO_TYPE_NAME = 'audios';

    const TAG_TYPE_NAME = 'tags';

    const INPUT_TYPE_NAME = 'input';

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

    public function getAvailableSnaps($code = null)
    {
        $snap = new Snap;
        if ($code) {
            return $snap->where('transaction_code', $code)->first();
        }

        return $snap->orderBy('created_at', 'desc')->paginate(100);
    }

    public function getSnapsByFilter($type, $mode)
    {
        return Snap::where('snap_type', '=', $type)
            ->where('mode_type', '=', $mode)
            ->paginate(50);
    }

    public function getSnapsByType($type)
    {
        return Snap::where('snap_type', '=', $type)
            ->paginate(50);
    }

    public function getSnapsByMode($mode)
    {
        return Snap::where('mode_type', '=', $mode)
            ->paginate(50);
    }

    public function getSnapFileById($id)
    {
        return SnapFile::with(['tag'])->where('id', '=', $id)->first();
    }

    public function getSnapByid($id)
    {
        return Snap::with(['files'])->where('id', $id)->first();
    }

    public function getSnapByCode($code)
    {
        return Snap::with(['files'])->where('request_code', $code)->first();
    }

    public function getSnapTagById($id)
    {
        return SnapTag::where('id', '=', $id)->first();
    }

    public function getSnapByLocation()
    {
        return Snap::where('location', null)
            ->orWhere('location', '')->get();
    }

    public function confirmSnap(Request $request, $id)
    {
        $snaps = $this->getSnapByid($id);

        if ($request->input('confirm') == 'approve') {
            //queue for calculate point
            (new PointService)->calculateApprovePoint($snaps);
            $snaps->approved_by = auth()->user()->id;
            $snaps->comment = $request->input('comment');
            $snaps->update();
        } elseif ($request->input('confirm') == 'reject') {
            $snaps->reject_by = auth()->user()->id;
            $snaps->comment = $request->input('comment');
            $snaps->update();
        }

        return true;
    }

    public function updateSnap(Request $request, $id)
    {
        $snaps = $this->getSnapByid($id);
        $snaps->receipt_id = $request->input('receipt_id');
        $snaps->location = $request->input('location');
        $snaps->purchase_time = $request->input('purchase_time');
        $snaps->outlet_name = $request->input('outlet_name');
        $snaps->outlet_type = $request->input('outlet_type');
        $snaps->outlet_city = $request->input('outlet_city');
        $snaps->outlet_province = $request->input('outlet_province');
        $snaps->outlet_zip_code = $request->input('outlet_zip_code');
        $snaps->payment_method = $request->input('payment_method');
        $snaps->longitude = !$request->has('longitude') ? 0.00 : $request->input('longitude');
        $snaps->latitude = !$request->has('latitude') ? 0.00 : $request->input('latitude');

        $snaps->update();
    }

    public function updateSnapModeInput(Request $request, $id)
    {
        $tags = $request->input('tag');
        $newTags = $request->input('newtag');
        $tagCount = count($tags['name']);
        $newTagCount = count($newTags['name']);
        $ids = $tags['id'];

        // Remove unnecessary snap tags
        $this->deleteSnapTags($ids, $id);

        // update tag.
        for ($i=0; $i < $tagCount; ++$i) {
            $tagId = $tags['id'][$i];
            $t = $this->getSnapTagById($tagId);
            $t->name = $tags['name'][$i];
            $t->brands = $tags['brands'][$i];
            $t->variants = $tags['variants'][$i];
            $t->quantity = $tags['qty'][$i];
            $t->total_price = $tags['total'][$i];
            $t->edited_signature = $this->generateSignature($tags['name'][$i], $tags['qty'][$i], $tags['total'][$i]);

            $t->update();
        }

        // create new tag
        for ($i=0; $i < $newTagCount; $i++) {
            $t = new SnapTag;
            $t->name = $newTags['name'][$i];
            $t->brands = $newTags['brands'][$i];
            $t->variants = $newTags['variants'][$i];
            $t->quantity = $newTags['qty'][$i];
            $t->total_price = $newTags['total'][$i];
            $t->file()->associate($newTags['fileId'][$i]);

            $t->save();
        }

        $this->totalValue($tags['total'], $newTags['total'], $id);
    }

    public function updateSnapModeTags(Request $request, $id)
    {
        $tags = $request->input('tag');
        $newTags = $request->input('newtag');
        $tagCount = count($tags['name']);
        $newTagCount = count($newTags['name']);
        $ids = $tags['id'];

        // Remove unnecessary snap tags
        $this->deleteSnapTags($ids, $id);

        // update tag.
        for ($i=0; $i < $tagCount; ++$i) {
            $tagId = $tags['id'][$i];
            $t = $this->getSnapTagById($tagId);
            $t->name = $tags['name'][$i];
            $t->brands = $tags['brands'][$i];
            $t->variants = $tags['variants'][$i];
            $t->quantity = $tags['qty'][$i];
            $t->total_price = $tags['total'][$i];
            $t->edited_signature = $this->generateSignature($tags['name'][$i], $tags['qty'][$i], $tags['total'][$i]);

            $t->update();
        }

        // create new tag
        for ($i=0; $i < $newTagCount; $i++) {
            $t = new SnapTag;
            $t->name = $newTags['name'][$i];
            $t->brands = $newTags['brands'][$i];
            $t->variants = $newTags['variants'][$i];
            $t->quantity = $newTags['qty'][$i];
            $t->total_price = $newTags['total'][$i];
            $t->img_x = $newTags['x'][$i];
            $t->img_y = $newTags['y'][$i];
            $t->file()->associate($newTags['fileId'][$i]);

            $t->save();
        }

        $this->totalValue($tags['total'], $newTags['total'], $id);
    }

    public function updateSnapModeAudios($request, $id)
    {
        $tags = $request->input('tag');
        $newTags = $request->input('newtag');
        $tagCount = count($tags['name']);
        $newTagCount = count($newTags['name']);
        $ids = $tags['id'];

        // Remove unnecessary snap tags
        $this->deleteSnapTags($ids, $id);

        // update tag.
        for ($i=0; $i < $tagCount; ++$i) {
            $tagId = $tags['id'][$i];
            $t = $this->getSnapTagById($tagId);
            $t->name = $tags['name'][$i];
            $t->brands = $tags['brands'][$i];
            $t->variants = $tags['variants'][$i];
            $t->quantity = $tags['qty'][$i];
            $t->total_price = $tags['total'][$i];
            $t->edited_signature = $this->generateSignature($tags['name'][$i], $tags['qty'][$i], $tags['total'][$i]);

            $t->update();
        }

        // create new tag
        for ($i=0; $i < $newTagCount; $i++) {
            $t = new SnapTag;
            $t->name = $newTags['name'][$i];
            $t->brands = $newTags['brands'][$i];
            $t->variants = $newTags['variants'][$i];
            $t->quantity = $newTags['qty'][$i];
            $t->total_price = $newTags['total'][$i];
            $t->file()->associate($newTags['fileId'][$i]);

            $t->save();
        }

        $this->totalValue($tags['total'], $newTags['total'], $id);
    }

    public function updateSnapModeImages($request, $id)
    {
        $tags = $request->input('tag');
        $newTags = $request->input('newtag');
        $tagCount = count($tags['name']);
        $newTagCount = count($newTags['name']);
        $ids = $tags['id'];

        // Remove unnecessary snap tags
        $this->deleteSnapTags($ids, $id);

        // update tag.
        for ($i=0; $i < $tagCount; ++$i) {
            $tagId = $tags['id'][$i];
            $t = $this->getSnapTagById($tagId);
            $t->name = $tags['name'][$i];
            $t->brands = $tags['brands'][$i];
            $t->variants = $tags['variants'][$i];
            $t->quantity = $tags['qty'][$i];
            $t->total_price = $tags['total'][$i];
            $t->edited_signature = $this->generateSignature($tags['name'][$i], $tags['qty'][$i], $tags['total'][$i]);

            $t->update();
        }

        // create new tag
        for ($i=0; $i < $newTagCount; $i++) {
            $t = new SnapTag;
            $t->name = $newTags['name'][$i];
            $t->brands = $newTags['brands'][$i];
            $t->variants = $newTags['variants'][$i];
            $t->quantity = $newTags['qty'][$i];
            $t->total_price = $newTags['total'][$i];
            $t->file()->associate($newTags['fileId'][$i]);

            $t->save();
        }

        $this->totalValue($tags['total'], $newTags['total'], $id);
    }

    public function generateSignature($name, $qty, $total)
    {
        return str_replace(' ', '', $name.'|'.$qty.'|'.clean_numeric($total, '%', false, '.'));
    }

    public function deleteSnapTags($ids, $snapFileId)
    {
        $ids = is_null($ids) ? [0] : $ids;
        SnapTag::where('snap_file_id', '=', $snapFileId)
                    ->whereNotIn('id', $ids)->delete();
    }

    public function totalValue($tagsTotal, $newTagsTotal, $id)
    {
        $tagsTotal = ($tagsTotal == true) ? $tagsTotal : array();
        $newTagsTotal = ($newTagsTotal == true) ? $newTagsTotal : array();

        $total = collect(array_merge($tagsTotal, $newTagsTotal))->sum();

        $snapFile = $this->getSnapFileById($id);
        $snapFile->total = $total;
        $snapFile->update();

        $snapId = $snapFile->snap_id;

        $snap = $this->getSnapByid($snapId);
        $snap->total_value = $snap->files->pluck('total')->sum();
        $snap->update();
    }

    public function handleMapAddress($latitude = 0.00000000, $longitude = 0.00000000)
    {
        $key = config('services.google.map.key');
        $mapProcess = (new GoogleMap($latitude, $longitude, $key));

        return $mapProcess->handle();
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

        if (! $this->presistData($request, $images)) {
            throw new Exception('Error when saving data in database!');
        }

        // send dispatcher
        $job = $this->getPlainDispatcher($data);
        dispatch($job);

        $dataSnap = [
            'request_code' => $request->input('request_code'),
            'member_id' => auth('api')->user()->id,
            'type' => 'receipt',
            'mode' => 'images',
            'files' => count($images),
        ];

        $this->saveEstimatedPoint($dataSnap);

        return $dataSnap;
    }

    /**
     * @param \Illuminate\Http\Request $request
     */
    public function generalTradeHandler(Request $request)
    {
        $images = $this->imagesProcess($request);
        if (0 === count($images)) {
            throw new SnapServiceException('There is no images was processed. Something wrong with the system!');
        }

        if ($this->isTagsMode($request)) {
            $mode = self::TAG_TYPE_NAME;

            DB::beginTransaction();
            try {
                $snap = $this->createSnap($request);
                $this->createFiles($request, $images, $snap);

                DB::commit();
            } catch (Exception $e) {
                DB::rollback();

                throw new SnapServiceException($e->getMessage());
            }

            $dataSnap = [
                'request_code' => $request->input('request_code'),
                'member_id' => auth('api')->user()->id,
                'type' => $request->input('snap_type'),
                'mode' => $mode,
                'files' => count($images),
            ];

            $this->saveEstimatedPoint($dataSnap);

            return $dataSnap;
        }

        if ($this->isAudioMode()) {
            $mode = self::AUDIO_TYPE_NAME;

            $dataSnap = [
                'request_code' => $request->input('request_code'),
                'member_id' => auth('api')->user()->id,
                'type' => $request->input('snap_type'),
                'mode' => $mode,
                'files' => count($images),
            ];

            $this->saveEstimatedPoint($dataSnap);

            return $dataSnap;
        }

        throw new Exception('Server Error');
    }

    /**
     * Snap handler for handwritten with input & audio mode
     *
     * @param  \Illuminate\Http\Request $request
     * @return array|mixed
     * @throws \App\Exceptions\Services\SnapServiceException
     */
    public function handWrittenHandler(Request $request)
    {
        $images = $this->imagesProcess($request);
        if (0 === count($images)) {
            throw new SnapServiceException('There is no images was processed. Something wrong with the system!');
        }

        if ($this->isInputMode($request)) {
            $mode = self::INPUT_TYPE_NAME;

            // just save data into database and return
            DB::beginTransaction();
            try {
                $snap = $this->createSnap($request);
                $this->createFiles($request, $images, $snap);

                DB::commit();
            } catch (Exception $e) {
                DB::rollback();
                throw new SnapServiceException($e->getMessage());
            }

            $dataSnap = [
                'request_code' => $request->input('request_code'),
                'member_id' => auth('api')->user()->id,
                'type' => $request->input('snap_type'),
                'mode' => $mode,
                'files' => count($images),
            ];

            $this->saveEstimatedPoint($dataSnap);

            return $dataSnap;
        }

        if ($this->isAudioMode($request)) {
            $mode = self::AUDIO_TYPE_NAME;
            $audios = $this->audiosProcess($request);

            if (empty($audios)) {
                throw new SnapServiceException('There is no audios was processed. Something wrong with the system!');
            }

            // build data
            $data = [
                'request_code' => $request->input('request_code'),
                'snap_type' => $request->input('snap_type'),
                'snap_mode' => $mode,
                'snap_files' => $audios,
            ];

            if (! $this->presistData($request, $images)) {
                throw new SnapServiceException('Error when saving data in database!');
            }

            // send dispatcher
            $job = $this->getPlainDispatcher($data);
            dispatch($job);

            $dataSnap = [
                'request_code' => $request->input('request_code'),
                'member_id' => auth('api')->user()->id,
                'type' => $request->input('snap_type'),
                'mode' => $mode,
                'files' => count($images),
            ];

            $this->saveEstimatedPoint($dataSnap);

            return $dataSnap;
        }

        throw new SnapServiceException('Server Error');
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
            throw new SnapServiceException('There is no audio to process!');
        }

        return $audioList;
    }

    /**
     * Upload file to s3.
     *
     * @param  array $files
     * @param  string $type
     * @return array
     */
    private function filesUploads(array $files, $type, $mimes = null)
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
                $fileList[$i]['file_mime'] = (null === $mimes) ? $file->getMimeType() : $mimes;

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
     * @return bool
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
        $snap->longitude = $request->input('longitude');
        $snap->latitude = $request->input('latitude');
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
                $snapFile = $this->persistFile($request, $file, $snap);

                $this->createTag($request, $snapFile);
            }

            return true;
        }

        $file = $this->persistFile($request, $files, $snap);
        $this->createTag($request, $file);

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
        $f->process_status = 'new';
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
        if (!$this->hasMode($request) && (!$this->isTagsMode($request) || !$this->isInputMode($request))) {
            return [];
        }

        $tags = $request->input(self::TAGS_FIELD_NAME);

        if ($tags != null) {
            foreach ($tags as $t) {
                $tag = new \App\SnapTag();
                $tag->name = $t['name'];
                $tag->total_price = $t['price'];
                $tag->quantity = $t['quantity'];
                $tag->img_x = isset($t['tag_x']) ? $t['tag_x'] : '';
                $tag->img_y = isset($t['tag_y']) ? $t['tag_y'] : '';
                $tag->current_signature = $this->generateSignature($t['name'], $t['quantity'], $t['price']);
                $tag->file()->associate($file);

                $tag->save();
            }
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
    private function isInputMode(Request $request)
    {
        if ($this->hasMode($request)) {
            return strtolower(self::INPUT_TYPE_NAME) === strtolower($request->input('mode_type')) ? true : false;
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

    /**
    * Save Estimated point
    * @param $data
    * @return bool
    */
    public function saveEstimatedPoint($data)
    {
        $requestCode = $data['request_code'];
        $memberId = $data['member_id'];
        $type = $data['type'];
        $mode = $data['mode'];
        $files = $data['files'];

        $point = (new PointService)->calculateEstimatedPoint($memberId, $type, $mode);
        $total = $point * $files;
        $snap = (new SnapService)->getSnapByCode($requestCode);
        $snap->estimated_point = $total;
        $snap->update();

        return true;
    }
}
