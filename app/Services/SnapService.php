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
use App\Events\TransactionEvent;
use App\Jobs\PointCalculation;
use App\Jobs\MemberActionJob;
use App\Jobs\AssignJob;
use App\Events\CrowdsourceEvent;
use App\Events\MemberActivityEvent;
use App\Member;

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

    const ACTION_BEHAVIOUR = 'snap_management';

    const NO_MODE_TYPE_NAME = 'no_mode';

    /**
     * @var string
     */
    private $s3Url;
    /**
     * @var bool
     */
    private $isNeedRecognition = true;
    /**
     * @var int
     */
    private $countOfTags = 0;
    /**
     * @var int
     */
    private $totalValue = 0;
    /**
     * @var int
     */
    private $estimatedPoint = 0;

    public function __construct()
    {
        $this->s3Url = env('S3_URL', '');
    }

    public function getAvailableSnaps()
    {
        return Snap::with('member')
                   ->with([
                       'files' => function ($query) {
                           $query->where('file_mimes', 'like', 'image%');
                       },
                   ])
                   ->orderBy('created_at', 'DESC')
                   ->paginate(50);
    }

    public function getAvailableSnapsByUser($id)
    {
        return Snap::with('member')
                   ->with([
                       'files' => function ($query) {
                           $query->where('file_mimes', 'like', 'image%');
                       },
                   ])
                   ->where('user_id', $id)
                   ->orderBy('created_at', 'DESC')
                   ->paginate(50);
    }

    public function getAvailableSnapByUserId($id)
    {
        return Snap::with('member')
                   ->with([
                       'files' => function ($query) {
                           $query->where('file_mimes', 'like', 'image%');
                       },
                   ])
                   ->where('user_id', $id)
                   ->orderBy('created_at', 'DESC')
                   ->paginate(50);
    }

    public function getSnapsByFilter($data, $userId = null)
    {
        $query = Snap::with([
            'files' => function ($query) {
                $query->where('file_mimes', 'like', 'image%');
            },
        ])
                     ->whereDate('created_at', '>=', $data['start_date'])
                     ->whereDate('created_at', '<=', $data['end_date']);

        if ($data['search'] == true) {
            $search = $data['search'];
            $query->whereHas('member', function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                      ->orWhere('email', 'like', '%' . $search . '%');
            });
        } else {
            $query->with('member');
        }

        if ($data['status'] != 'all') {
            $query->where('status', '=', $data['status']);
        }

        if ($data['mode_type']) {
            $query->where('mode_type', '=', $data['mode_type']);
        }

        if ($data['snap_type']) {
            $query->where('snap_type', '=', $data['snap_type']);
        }

        if ($userId != null) {
            $query->where('user_id', '=', $userId);
        }

        $query->orderBy('created_at', 'DESC');


        return $query->paginate(50);
    }

    public function getExportSnapToCsv($data, $userId = null)
    {
        $query = Snap::with([
            'files' => function ($query) {
                $query->where('file_mimes', 'like', 'image%');
            },
        ])
                     ->with('approve')
                     ->with('reject')
                     ->whereDate('created_at', '>=', $data['start_date'])
                     ->whereDate('created_at', '<=', $data['end_date']);

        if ($data['search'] == true) {
            $search = $data['search'];
            $query->whereHas('member', function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                      ->orWhere('email', 'like', '%' . $search . '%');
            });
        } else {
            $query->with('member');
        }

        if ($data['status'] != 'all') {
            $query->where('status', '=', $data['status']);
        }

        if ($data['mode_type']) {
            $query->where('mode_type', '=', $data['mode_type']);
        }

        if ($data['snap_type']) {
            $query->where('snap_type', '=', $data['snap_type']);
        }

        if ($userId != null) {
            $query->where('user_id', '=', $userId);
        }

        $query->orderBy('created_at', 'DESC');

        $r = $query->paginate(100);

        $files = [];
        $snapFileIds = [];
        foreach ($r as $value) {
            $snaps[$value->id]['main'] = $value;

            foreach ($value->files as $file) {
                $snapFileIds[] = $file->id;
                $files[$file->id] = $value->id;
            }
        }

        $snapTags = SnapTag::whereIn('snap_file_id', $snapFileIds)
                           ->get();

        foreach ($snapTags as $tag) {
            $snaps[$files[$tag->snap_file_id]]['tags'][] = $tag;
        }

        $results = [];
        foreach ($snaps as $snap) {
            $ocr = [];
            foreach ($snap['main']->files as $file) {
                $ocr[] = $file->recognition_text;
            }

            $ocr = implode(' ', $ocr);
            $approve = isset($snap['main']->approve) ? $snap['main']->approve->name : null;
            $reject = isset($snap['main']->reject) ? $snap['main']->reject->name : null;
            $type = ($snap['main']->snap_type == 'receipt') ? strtoupper($snap['main']->snap_type) : strtoupper($snap['main']->snap_type) . ' Snap with ' . strtoupper($snap['main']->mode_type) . ' mode';
            $comment = explode('\n', $snap['main']->comment);

            if (isset($snap['tags'])) {
                foreach ($snap['tags'] as $tag) {

                    $results[] = [
                        'snap_code' => $snap['main']->request_code,
                        'type' => $type,
                        'status' => $snap['main']->status,
                        'reason' => isset($comment[1]) ? $comment[1] : '',
                        'of_images' => $snap['main']->files->count(),
                        'email' => $snap['main']->member->email,
                        'name' => $snap['main']->member->name,
                        'estimated_point' => $snap['main']->estimated_point,
                        'fixed_point' => $snap['main']->fixed_point,
                        'current_point' => $snap['main']->current_point_member,
                        'current_level' => $snap['main']->current_level_member,
                        'snapped' => $snap['main']->created_at->toDateTimeString(),
                        'approve_or_reject_date' => $snap['main']->updated_at->toDateTimeString(),
                        'approve_or_reject_by' => isset($approve) ? $approve : $reject,
                        'ocr' => trim(str_replace(["\n", "\r", ","], ' ', $ocr)),
                        'product_name' => trim(str_replace(["\n", "\r", ","], ' ', $tag->name)),
                        'brands' => trim(str_replace(["\n", "\r", ","], ' ', $tag->brands)),
                        'weight' => trim(str_replace(["\n", "\r", ","], ' ', $tag->weight)),
                        'sku' => trim(str_replace(["\n", "\r", ","], ' ', $tag->sku)),
                        'quantity' => trim(str_replace(["\n", "\r", ","], ' ', $tag->quantity)),
                        'total_price' => trim(str_replace(["\n", "\r", ","], ' ', $tag->total_price)),
                    ];
                }
            } else {
                $results[] = [
                    'snap_code' => $snap['main']->request_code,
                    'type' => $type,
                    'status' => $snap['main']->status,
                    'reason' => isset($comment[1]) ? $comment[1] : '',
                    'of_images' => $snap['main']->files->count(),
                    'email' => $snap['main']->member->email,
                    'name' => $snap['main']->member->name,
                    'estimated_point' => $snap['main']->estimated_point,
                    'fixed_point' => $snap['main']->fixed_point,
                    'current_point' => $snap['main']->current_point_member,
                    'current_level' => $snap['main']->current_level_member,
                    'snapped' => $snap['main']->created_at->toDateTimeString(),
                    'approve_or_reject_date' => $snap['main']->updated_at->toDateTimeString(),
                    'approve_or_reject_by' => isset($approve) ? $approve : $reject,
                    'ocr' => trim(str_replace(["\n", "\r", ","], ' ', $ocr)),
                    'product_name' => '',
                    'brands' => '',
                    'weight' => '',
                    'sku' => '',
                    'quantity' => '',
                    'total_price' => '',
                ];
            }

        }

        if ($data['type'] == 'new') {
            $filename = strtolower(str_random(10)) . '.csv';
            $title = 'No,Snap Code,Type,# of images,User Details,Name,Estimated Point,Fixed Point,Current Point,Current Level,Aproved / Rejected,Rejection Reason,Receipt Snapped,Approved / Rejected Date,Approved / Rejected By,Product Name,Brands,Weight,SKU,Quantity,Total Price,OCR';
            \Storage::disk('csv')->put($filename, $title);
            $no = 1;
            foreach ($results as $row) {
                $baris = $no . ',' . $row['snap_code'] . ',' . $row['type'] . ',' . $row['of_images'] . ',' . $row['email'] . ',' . $row['name'] . ',' . $row['estimated_point'] . ',' . $row['fixed_point'] . ',' . $row['current_point'] . ',' . $row['current_level'] . ',' . $row['status'] . ',' . $row['reason'] . ',' . $row['snapped'] . ',' . $row['approve_or_reject_date'] . ',' . $row['approve_or_reject_by'] . ',' . $row['product_name'] . ',' . $row['brands'] . ',' . $row['weight'] . ',' . $row['sku'] . ',' . $row['quantity'] . ',' . $row['total_price'] . ',' . $row['ocr'];
                \Storage::disk('csv')->append($filename, $baris);
                $no++;
            }

        } else {
            if ($data['type'] == 'next') {
                $filename = $data['filename'];
                $no = $data['no'];
                foreach ($results as $row) {
                    $baris = $no . ',' . $row['snap_code'] . ',' . $row['type'] . ',' . $row['of_images'] . ',' . $row['email'] . ',' . $row['name'] . ',' . $row['estimated_point'] . ',' . $row['fixed_point'] . ',' . $row['current_point'] . ',' . $row['current_level'] . ',' . $row['status'] . ',' . $row['reason'] . ',' . $row['snapped'] . ',' . $row['approve_or_reject_date'] . ',' . $row['approve_or_reject_by'] . ',' . $row['product_name'] . ',' . $row['brands'] . ',' . $row['weight'] . ',' . $row['sku'] . ',' . $row['quantity'] . ',' . $row['total_price'] . ',' . $row['ocr'];
                    \Storage::disk('csv')->append($filename, $baris);
                    $no++;
                }
            }
        }

        $lastPage = $r->lastPage();
        $params = [
            'type_request' => ($lastPage == $data['page'] || count($snaps) == 0) ? 'download' : 'next',
            'filename' => $filename,
            'page' => $data['page'] + 1,
            'no' => $no,
            'last' => $lastPage,
        ];

        return $params;
    }

    public function getSnapsBySearch($search, $userId = null)
    {
        return ($userId == null) ?
            Snap::whereHas('member', function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                      ->orWhere('email', 'like', '%' . $search . '%');
            })
                ->with([
                    'files' => function ($query) {
                        $query->where('file_mimes', 'like', 'image%');
                    },
                ])
                ->orWhere('request_code', $search)
                ->orderBy('created_at', 'DESC')
                ->paginate(50) :
            Snap::whereHas('member', function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                      ->orWhere('email', 'like', '%' . $search . '%');
            })
                ->with([
                    'files' => function ($query) {
                        $query->where('file_mimes', 'like', 'image%');
                    },
                ])
                ->where('user_id', '=', $userId)
                ->orWhere('request_code', $search)
                ->orderBy('created_at', 'DESC')
                ->paginate(50);
    }

    public function getSnapFileById($id)
    {
        return SnapFile::with(['tag'])->where('id', '=', $id)->first();
    }

    public function getSnapFileByPosition($snapId, $position)
    {
        return SnapFile::where('snap_id', $snapId)
                       ->where('position', $position)
                       ->orderBy('id', 'DESC')
                       ->first();
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
                   ->orWhere('location', '')
                   ->whereNotIn('longitude', ['0.00000000'])
                   ->whereNotIn('latitude', ['0.00000000'])
                   ->take(20)
                   ->orderBy('id', 'DESC')
                   ->get();
    }

    public function confirmSnap(Request $request, $id)
    {
        $snaps = $this->getSnapByid($id);
        $userId = auth()->user()->id;
        $comment = $request->input('comment');
        $point = $request->input('point');
        $promo = $request->input('promo');

        if (!empty($request->input('reason'))) {
            if ($request->has('other')) {
                $settingName = 'Snap Reason';
                $reason = $request->input('other');
                $setting = new \App\Setting;
                $setting->setting_group = strtolower(str_replace(' ', '_', $settingName));
                // $setting->setting_name = strtolower($settingName);
                $setting->setting_display_name = $settingName;
                $setting->setting_value = trim($reason);
                $setting->setting_type = 'toggle';
                $setting->is_visible = 1;
                $setting->created_by = $userId;
                $setting->save();

                // generate rejection_code
                $setting->setting_name = 'R' . str_pad($setting->id, 3, '0', STR_PAD_LEFT);
                $setting->save();

            } else {
                $reasonId = $request->input('reason');
                $setting = \App\Setting::where('id', $reasonId)
                    ->first();
                $reason = $setting->setting_value;
            }

            $comment = nl2br($comment . ' \n Alasan :' . $reason);
        }

        if ($request->input('confirm') == 'approve') {
            //queue for calculate point
            $config = config('common.queue_list.point_process');
            $job = (new PointCalculation($snaps, $point, $promo))->onQueue($config)->onConnection(env('INFOSCAN_QUEUE'));
            dispatch($job);
            $snaps->approved_by = $userId;
            $snaps->comment = $comment;
            $snaps->update();

            $tags = $this->getCountOfTags($snaps->files);
            $data = [
                'action' => 'approve',
                'data' => [
                    'snap_id' => $snaps->id,
                    'snap_code' => $snaps->request_code,
                    'comment' => $snaps->comment,
                    'point' => $point,
                    'add_tag' => $tags['crowdsource_add'],
                    'edit_tag' => $tags['crowdsource_edit'],
                ],
            ];

        } elseif ($request->input('confirm') == 'reject') {
            $snaps->reject_by = $userId;
            $snaps->comment = $comment;
            $snaps->status = 'reject';
            $snaps->rejection_code = $setting->setting_name;
            $snaps->fixed_point = '0';
            $snaps->update();

            $tags = $this->getCountOfTags($snaps->files);
            $data = [
                'action' => 'reject',
                'data' => [
                    'snap_id' => $snaps->id,
                    'snap_code' => $snaps->request_code,
                    'comment' => $snaps->comment,
                    'point' => $request->input('point'),
                    'add_tag' => $tags['crowdsource_add'],
                    'edit_tag' => $tags['crowdsource_edit'],
                ],
            ];

        }
        $originalData = $data;

        $data = json_encode($data);
        // event for crowdsource log
        event(new CrowdsourceEvent($userId, self::ACTION_BEHAVIOUR, $data));

        //build data for member history
        $content = [
            'type' => $snaps->mode_type,
            'title' => $this->getType($snaps->snap_type),
            'description' => $request->input('comment'),
            'image' => $snaps->files[0]->file_path,
            'status' => $request->input('confirm'),
        ];

        $config = config('common.queue_list.member_action_log');
        $job = (new MemberActionJob($snaps->member_id, 'snap', $content))->onQueue($config)->onConnection(env('INFOSCAN_QUEUE'));
        dispatch($job);


        if ('reject' === $originalData['action']) {
            $message = config('common.notification_messages.snaps.failed');
            (new NotificationService($message))->setUser($snaps->member_id)
                                               ->setData([
                                                   'action' => 'history',
                                               ])->send();
        }

        return true;
    }

    public function updateSnap(Request $request, $id)
    {
        $snaps = $this->getSnapByid($id);
        // $firstFileId = $snaps->files->first()->id;
        // $tags = $request->input('tag');

        // //delete tag
        // if ($snaps->mode_type == 'tags' || $snaps->mode_type == 'input') {
        //     $this->deleteTagsOrInput($request, $id);
        // }
        // //update and new tag
        // $this->updateSnapModeInput($request, $firstFileId);

        $snaps->receipt_id = $request->input('receipt_id');
        $snaps->location = $request->input('location');
        $snaps->purchase_time = $request->input('purchase_time');
        $snaps->outlet_name = $request->input('outlet_name');
        $snaps->outlet_type = $request->input('outlet_type');
        $snaps->outlet_city = $request->input('outlet_city');
        $snaps->outlet_province = $request->input('outlet_province');
        $snaps->outlet_rt_rw = $request->input('outlet_rt_rw');
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
        for ($i = 0; $i < $tagCount; ++$i) {
            $tagId = $tags['id'][$i];
            $t = $this->getSnapTagById($tagId);
            $t->name = $tags['name'][$i];
            $t->brands = $tags['brands'][$i];
            $t->variants = $tags['variants'][$i];
            $t->weight = $tags['weight'][$i];
            $t->quantity = $tags['qty'][$i];
            $t->total_price = $tags['total'][$i];
            $t->crop_file_path = isset($tags['crop_path'][$i]) ? $tags['crop_path'][$i] : null;
            $t->edited_signature = $this->generateSignature($tags['name'][$i], $tags['weight'][$i], $tags['qty'][$i], $tags['total'][$i]);

            $t->update();
        }

        // create new tag
        for ($i = 0; $i < $newTagCount; $i++) {
            $t = new SnapTag;
            $t->name = $newTags['name'][$i];
            $t->brands = $newTags['brands'][$i];
            $t->variants = $newTags['variants'][$i];
            $t->weight = $newTags['weight'][$i];
            $t->quantity = $newTags['qty'][$i];
            $t->total_price = $newTags['total'][$i];
            $t->crop_file_path = isset($newTags['crop_path'][$i]) ? $newTags['crop_path'][$i] : null;
            $t->file()->associate($id);

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
        for ($i = 0; $i < $tagCount; ++$i) {
            $tagId = $tags['id'][$i];
            $t = $this->getSnapTagById($tagId);
            $t->name = $tags['name'][$i];
            $t->brands = $tags['brands'][$i];
            //$t->variants = $tags['variants'][$i];
            $t->sku = $tags['sku'][$i];
            $t->weight = $tags['weight'][$i];
            $t->quantity = $tags['qty'][$i];
            $t->total_price = $tags['total'][$i];
            $t->crop_file_path = isset($tags['crop_path'][$i]) ? $tags['crop_path'][$i] : null;
            $t->edited_signature = $this->generateSignature($tags['name'][$i], $tags['weight'][$i], $tags['qty'][$i], $tags['total'][$i]);

            $t->update();
        }

        // create new tag
        for ($i = 0; $i < $newTagCount; $i++) {
            $t = new SnapTag;
            $t->name = $newTags['name'][$i];
            $t->brands = $newTags['brands'][$i];
            // $t->variants = $newTags['variants'][$i];
            $t->sku = $newTags['sku'][$i];
            $t->weight = $newTags['weight'][$i];
            $t->quantity = $newTags['qty'][$i];
            $t->total_price = $newTags['total'][$i];
            $t->img_x = $newTags['x'][$i];
            $t->img_y = $newTags['y'][$i];
            $t->crop_file_path = isset($newTags['crop_path'][$i]) ? $newTags['crop_path'][$i] : null;
            $t->file()->associate($id);

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
        for ($i = 0; $i < $tagCount; ++$i) {
            $tagId = $tags['id'][$i];
            $t = $this->getSnapTagById($tagId);
            $t->name = $tags['name'][$i];
            $t->brands = $tags['brands'][$i];
            // $t->variants = $tags['variants'][$i];
            $t->sku = $tags['sku'][$i];
            $t->weight = $tags['weight'][$i];
            $t->quantity = $tags['qty'][$i];
            $t->total_price = $tags['total'][$i];
            $t->crop_file_path = isset($tags['crop_path'][$i]) ? $tags['crop_path'][$i] : null;
            $t->edited_signature = $this->generateSignature($tags['name'][$i], $tags['weight'][$i], $tags['qty'][$i], $tags['total'][$i]);

            $t->update();
        }

        // create new tag
        for ($i = 0; $i < $newTagCount; $i++) {
            $t = new SnapTag;
            $t->name = $newTags['name'][$i];
            $t->brands = $newTags['brands'][$i];
            // $t->variants = $newTags['variants'][$i];
            $t->sku = $newTags['sku'][$i];
            $t->weight = $newTags['weight'][$i];
            $t->quantity = $newTags['qty'][$i];
            $t->total_price = $newTags['total'][$i];
            $t->crop_file_path = isset($newTags['crop_path'][$i]) ? $newTags['crop_path'][$i] : null;
            $t->file()->associate($id);

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
        for ($i = 0; $i < $tagCount; ++$i) {
            $tagId = $tags['id'][$i];
            $t = $this->getSnapTagById($tagId);
            $t->name = $tags['name'][$i];
            $t->brands = $tags['brands'][$i];
            // $t->variants = $tags['variants'][$i];
            $t->sku = $tags['sku'][$i];
            $t->weight = $tags['weight'][$i];
            $t->quantity = $tags['qty'][$i];
            $t->total_price = $tags['total'][$i];
            $t->crop_file_path = isset($tags['crop_path'][$i]) ? $tags['crop_path'][$i] : null;
            $t->edited_signature = $this->generateSignature($tags['name'][$i], $tags['weight'][$i], $tags['qty'][$i], $tags['total'][$i]);

            $t->update();
        }

        // create new tag
        for ($i = 0; $i < $newTagCount; $i++) {
            $t = new SnapTag;
            $t->name = $newTags['name'][$i];
            $t->brands = $newTags['brands'][$i];
            // $t->variants = $newTags['variants'][$i];
            $t->sku = $newTags['sku'][$i];
            $t->weight = $newTags['weight'][$i];
            $t->quantity = $newTags['qty'][$i];
            $t->total_price = $newTags['total'][$i];
            $t->crop_file_path = isset($newTags['crop_path'][$i]) ? $newTags['crop_path'][$i] : null;
            $t->file()->associate($id);

            $t->save();
        }

        $this->totalValue($tags['total'], $newTags['total'], $id);
    }

    public function generateSignature($name, $weight, $qty, $total)
    {
        return str_replace(' ', '',
            trim($name) . '|' . trim($weight) . '|' . trim($qty) . '|' . clean_numeric(trim($total), '%', false, '.'));
    }

    public function deleteSnapTags($ids, $snapFileId)
    {
        $ids = is_null($ids) ? [0] : $ids;
        SnapTag::where('snap_file_id', '=', $snapFileId)
               ->whereNotIn('id', $ids)->delete();
    }

    public function totalValue($tagsTotal, $newTagsTotal, $id)
    {
        $tagsTotal = ($tagsTotal == true) ? $tagsTotal : [];
        $newTagsTotal = ($newTagsTotal == true) ? $newTagsTotal : [];

        $total = collect(array_merge($tagsTotal, $newTagsTotal))->sum();

        $snapFile = $this->getSnapFileById($id);
        $snapFile->total = $total;
        $snapFile->update();

        $snapId = $snapFile->snap_id;

        $snap = $this->getSnapByid($snapId);
        $snap->total_value = $snap->files->pluck('total')->sum();
        $snap->update();

        $this->setTotalValue($snap->total_value);
    }

    public function handleMapAddress($latitude = 0.00000000, $longitude = 0.00000000)
    {
        $key = config('services.google.map.key');
        $mapProcess = (new GoogleMap($latitude, $longitude, $key));

        return $mapProcess->handle();
    }

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return array|mixed
     * @throws \App\Exceptions\Services\SnapServiceException
     * @throws \Exception
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

        if (!$snap = $this->presistData($request, $images)) {
            throw new Exception('Error when saving data in database!');
        }

        // get code task
        $code = $this->getCodeTask($snap->snap_type, $snap->mode_type);

        $task = $this->getTaskPointByCode($code, 'receipt');

        $point = isset($task->point) ? $task->point : 0;

        // send dispatcher
        $job = $this->getPlainDispatcher($data);
        dispatch($job);

        // Auth Member
        $member = auth('api')->user();
        $tags = $this->getTags();

        $dataSnap = [
            'request_code' => $request->input('request_code'),
            'member_id' => $member->id,
            'type' => $request->input('snap_type'),
            'mode' => 'images',
            'files' => count($images),
            'tags' => count($tags),
            'point' => $point,
        ];

        // Save estimated point calculate
        $estimatedPoint = $this->saveEstimatedPoint($dataSnap);

        $dataPoint = [
            'estimated_point' => $estimatedPoint,
        ];

        //build data to save member log
        $data = [
            'action' => $request->input('snap_type'),
            'data' => array_merge($dataSnap, $dataPoint),
        ];

        $data = json_encode($data);

        // Member log
        event(new MemberActivityEvent($member->member_code, self::ACTION_BEHAVIOUR, $data));

        //assign to crowdsource
        $this->assignToCrowdsource();

        //build data for member history
        $this->memberSnapHistory($snap);

        // send notification
        $this->sendSnapNotification('pending', $this->estimatedPoint);

        return $dataSnap;
    }

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     * @throws \App\Exceptions\Services\SnapServiceException
     * @throws \Exception
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
                // get code task
                $code = $this->getCodeTask($snap->snap_type, $snap->mode_type);

                $task = $this->getTaskPointByCode($code);

                $point = isset($task->point) ? $task->point : 0;

                $this->createFiles($request, $images, $snap, $point);

                DB::commit();
            } catch (Exception $e) {
                DB::rollback();

                throw new SnapServiceException($e->getMessage());
            }

            // Auth Member
            $member = auth('api')->user();
            $tags = $this->getTags();

            $dataSnap = [
                'request_code' => $request->input('request_code'),
                'member_id' => $member->id,
                'type' => $request->input('snap_type'),
                'mode' => $mode,
                'files' => count($images),
                'tags' => count($tags),
                'point' => $point,
            ];

            // Save estimated point calculate
            $estimatedPoint = $this->saveEstimatedPoint($dataSnap);

            $dataPoint = [
                'estimated_point' => $estimatedPoint,
            ];

            //build data to save member log
            $data = [
                'action' => $request->input('snap_type'),
                'data' => array_merge($dataSnap, $dataPoint),
            ];

            $data = json_encode($data);

            // Member log
            event(new MemberActivityEvent($member->member_code, self::ACTION_BEHAVIOUR, $data));

            //assign to crowdsource
            $this->assignToCrowdsource();

            //build data for member history
            $this->memberSnapHistory($snap, $images);

            // send notification
            $this->sendSnapNotification('pending', $this->estimatedPoint);

            return $dataSnap;
        }

        if ($this->isAudioMode($request)) {
            $mode = self::AUDIO_TYPE_NAME;
            $audios = $this->audiosProcess($request);

            if (empty($audios)) {
                throw new SnapServiceException('There is no audios was processed. Something wrong with the system!');
            }

            DB::beginTransaction();
            try {
                $snap = $this->createSnap($request);
                // get code task
                $code = $this->getCodeTask($snap->snap_type, $snap->mode_type);

                $task = $this->getTaskPointByCode($code);

                $point = isset($task->point) ? $task->point : 0;

                $this->createFiles($request, $images, $snap, $point);
                $this->createFiles($request, $audios, $snap);

                DB::commit();
            } catch (Exception $e) {
                DB::rollback();

                throw new SnapServiceException($e->getMessage());
            }

            // Auth Member
            $member = auth('api')->user();
            $tags = $this->getTags();

            $dataSnap = [
                'request_code' => $request->input('request_code'),
                'member_id' => $member->id,
                'type' => $request->input('snap_type'),
                'mode' => $mode,
                'files' => count($images),
                'tags' => count($tags),
                'point' => $point,
            ];

            // Save estimated point calculate
            $estimatedPoint = $this->saveEstimatedPoint($dataSnap);

            $dataPoint = [
                'estimated_point' => $estimatedPoint,
            ];

            //build data to save member log
            $data = [
                'action' => $request->input('snap_type'),
                'data' => array_merge($dataSnap, $dataPoint),
            ];

            $data = json_encode($data);

            // Member log
            event(new MemberActivityEvent($member->member_code, self::ACTION_BEHAVIOUR, $data));

            //assign to crowdsource
            $this->assignToCrowdsource();

            //build data for member history
            $this->memberSnapHistory($snap, $images);

            // send notification
            $this->sendSnapNotification('pending', $this->estimatedPoint);

            return $dataSnap;
        }

        if ($this->isNoMode($request)) {
            $mode = self::NO_MODE_TYPE_NAME;

            DB::beginTransaction();
            try {
                $snap = $this->createSnap($request);
                // get code task
                $code = $this->getCodeTask($snap->snap_type, $snap->mode_type);

                $task = $this->getTaskPointByCode($code);

                $point = isset($task->point) ? $task->point : 0;

                $this->createFiles($request, $images, $snap, $point);

                DB::commit();
            } catch (Exception $e) {
                DB::rollback();

                throw new SnapServiceException($e->getMessage());
            }

            // Auth Member
            $member = auth('api')->user();
            $tags = $this->getTags();

            $dataSnap = [
                'request_code' => $request->input('request_code'),
                'member_id' => $member->id,
                'type' => $request->input('snap_type'),
                'mode' => $mode,
                'files' => count($images),
                'tags' => count($tags),
                'point' => $point,
            ];

            // Save estimated point calculate
            $estimatedPoint = $this->saveEstimatedPoint($dataSnap);

            $dataPoint = [
                'estimated_point' => $estimatedPoint,
            ];

            //build data to save member log
            $data = [
                'action' => $request->input('snap_type'),
                'data' => array_merge($dataSnap, $dataPoint),
            ];

            $data = json_encode($data);

            // Member log
            event(new MemberActivityEvent($member->member_code, self::ACTION_BEHAVIOUR, $data));

            //assign to crowdsource
            $this->assignToCrowdsource();

            //build data for member history
            $this->memberSnapHistory($snap, $images);

            // send notification
            $this->sendSnapNotification('pending', $this->estimatedPoint);

            return $dataSnap;
        }

        throw new Exception('Server Error');
    }

    /**
     * Snap handler for handwritten with input & audio mode
     *
     * @param  \Illuminate\Http\Request $request
     *
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
                // get code task
                $code = $this->getCodeTask($snap->snap_type, $snap->mode_type);

                $task = $this->getTaskPointByCode($code);

                $point = isset($task->point) ? $task->point : 0;

                $this->createFiles($request, $images, $snap, $point);

                DB::commit();
            } catch (Exception $e) {
                DB::rollback();
                throw new SnapServiceException($e->getMessage());
            }

            // Auth Member
            $member = auth('api')->user();
            $tags = $this->getTags();

            $countTags = [];
            foreach ($tags as $tag) {
                $countTags[] = count($tag);
            }

            $dataSnap = [
                'request_code' => $request->input('request_code'),
                'member_id' => $member->id,
                'type' => $request->input('snap_type'),
                'mode' => $mode,
                'files' => count($images),
                'tags' => array_sum($countTags),
                'point' => $point,
            ];

            // Save estimated point calculate
            $estimatedPoint = $this->saveEstimatedPoint($dataSnap);

            $dataPoint = [
                'estimated_point' => $estimatedPoint,
            ];

            //build data to save member log
            $data = [
                'action' => $request->input('snap_type'),
                'data' => array_merge($dataSnap, $dataPoint),
            ];

            $data = json_encode($data);

            // Member log
            event(new MemberActivityEvent($member->member_code, self::ACTION_BEHAVIOUR, $data));

            //assign to crowdsource
            $this->assignToCrowdsource();

            //build data for member history
            $this->memberSnapHistory($snap, $images);

            // send notification
            $this->sendSnapNotification('pending', $this->estimatedPoint);

            return $dataSnap;
        }

        if ($this->isAudioMode($request)) {
            $mode = self::AUDIO_TYPE_NAME;
            $audios = $this->audiosProcess($request);

            if (empty($audios)) {
                throw new SnapServiceException('There is no audios was processed. Something wrong with the system!');
            }

            DB::beginTransaction();
            try {
                $snap = $this->createSnap($request);
                // get code task
                $code = $this->getCodeTask($snap->snap_type, $snap->mode_type);

                $task = $this->getTaskPointByCode($code);

                $point = isset($task->point) ? $task->point : 0;

                $this->createFiles($request, $images, $snap, $point);
                $this->createFiles($request, $audios, $snap);

                DB::commit();
            } catch (Exception $e) {
                DB::rollback();

                throw new SnapServiceException($e->getMessage());
            }

            // build data
            $data = [
                'request_code' => $request->input('request_code'),
                'snap_type' => $request->input('snap_type'),
                'snap_mode' => $mode,
                'snap_files' => $audios,
            ];

            /*if (! $snap = $this->presistData($request, $images)) {
                throw new SnapServiceException('Error when saving data in database!');
            }*/

            // send dispatcher
            $job = $this->getPlainDispatcher($data);
            dispatch($job);

            // Auth Member
            $member = auth('api')->user();
            $tags = $this->getTags();

            $dataSnap = [
                'request_code' => $request->input('request_code'),
                'member_id' => $member->id,
                'type' => $request->input('snap_type'),
                'mode' => $mode,
                'files' => count($images),
                'tags' => count($tags),
                'point' => $point,
            ];

            // Save estimated point calculate
            $estimatedPoint = $this->saveEstimatedPoint($dataSnap);

            $dataPoint = [
                'estimated_point' => $estimatedPoint,
            ];

            //build data to save member log
            $data = [
                'action' => $request->input('snap_type'),
                'data' => array_merge($dataSnap, $dataPoint),
            ];

            $data = json_encode($data);

            // Member log
            event(new MemberActivityEvent($member->member_code, self::ACTION_BEHAVIOUR, $data));

            //assign to crowdsource
            $this->assignToCrowdsource();

            //build data for member history
            $this->memberSnapHistory($snap, $images);

            // send notification
            $this->sendSnapNotification('pending', $this->estimatedPoint);

            return $dataSnap;
        }

        if ($this->isNoMode($request)) {
            $mode = self::NO_MODE_TYPE_NAME;

            DB::beginTransaction();
            try {
                $snap = $this->createSnap($request);
                // get code task
                $code = $this->getCodeTask($snap->snap_type, $snap->mode_type);

                $task = $this->getTaskPointByCode($code);

                $point = isset($task->point) ? $task->point : 0;

                $this->createFiles($request, $images, $snap, $point);

                DB::commit();
            } catch (Exception $e) {
                DB::rollback();

                throw new SnapServiceException($e->getMessage());
            }

            // Auth Member
            $member = auth('api')->user();
            $tags = $this->getTags();

            $dataSnap = [
                'request_code' => $request->input('request_code'),
                'member_id' => $member->id,
                'type' => $request->input('snap_type'),
                'mode' => $mode,
                'files' => count($images),
                'tags' => count($tags),
                'point' => $point,
            ];

            // Save estimated point calculate
            $estimatedPoint = $this->saveEstimatedPoint($dataSnap);

            $dataPoint = [
                'estimated_point' => $estimatedPoint,
            ];

            //build data to save member log
            $data = [
                'action' => $request->input('snap_type'),
                'data' => array_merge($dataSnap, $dataPoint),
            ];

            $data = json_encode($data);

            // Member log
            event(new MemberActivityEvent($member->member_code, self::ACTION_BEHAVIOUR, $data));

            //assign to crowdsource
            $this->assignToCrowdsource();

            //build data for member history
            $this->memberSnapHistory($snap, $images);

            // send notification
            $this->sendSnapNotification('pending', $this->estimatedPoint);

            return $dataSnap;
        }

        throw new SnapServiceException('Server Error');
    }

    /**
     * Store all image to local storage and upload the to s3 by event jobs (queue).
     *
     * @param  Request $request
     *
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
     *
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
     * @param  array  $files
     * @param  string $type
     *
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
                $fileList[$i]['position'] = $i;

                ++$i;
            }
        }

        return $fileList;
    }

    /**
     * @param \Illuminate\Http\Request $request
     *
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
     *
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
     * @param array                    $files
     *
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

        return $snap;
    }

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return \App\Snap;
     */
    private function createSnap(Request $request)
    {
        $snap = new \App\Snap;
        $snap->request_code = $request->input('request_code');
        $snap->member_id = auth('api')->user()->id;
        $snap->snap_type = $request->input('snap_type');
        if ($request->exists('mode_type') || 'receipt' !== strtolower($request->input('snap_type'))) {
            $snap->mode_type = $request->input('mode_type');
        }
        $snap->status = 'pending';
        $snap->longitude = $request->input('longitude');
        $snap->latitude = $request->input('latitude');
        $snap->current_point_member = auth('api')->user()->temporary_point;
        $snap->current_level_member = auth('api')->user()->temporary_level;
        $snap->save();

        return $snap;
    }

    /**
     * @param           $request
     * @param array     $files
     * @param \App\Snap $snap
     *
     * @return bool
     */
    private function createFiles($request, array $files, \App\Snap $snap, $point = null)
    {
        if ($this->isMultidimensiArray($files)) {
            if ($request->input('mode_type') == self::INPUT_TYPE_NAME) {
                $tags = [];
                foreach ($files as $file) {
                    $snapFile = $this->persistFile($request, $file, $snap, $point);

                    $this->createTag($request, $snapFile);
                    $tags[] = $this->getTags();
                }

                $this->setTags($tags);
            } else {
                foreach ($files as $file) {
                    $snapFile = $this->persistFile($request, $file, $snap, $point);

                    $this->createTag($request, $snapFile);
                }
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
     * @param array                    $data
     * @param                          $snap
     *
     * @return \App\SnapFile
     */
    private function persistFile(Request $request, array $data, $snap, $point = null)
    {
        $f = new \App\SnapFile();
        $f->file_path = $data['filename'];
        $f->file_code = $data['file_code'];
        $f->file_mimes = $data['file_mime'];
        $f->position = $data['position'];
        $f->file_dimension = null;
        $f->process_status = 'new';
        if ($this->hasMode($request)) {
            $f->mode_type = $request->input('mode_type');
        }

        $f->image_point = $point;

        $f->snap()->associate($snap);
        $f->save();

        return $f;
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\SnapFile            $file
     *
     * @return array|string
     */
    private function createTag(Request $request, \App\SnapFile $file)
    {
        if (!$this->hasMode($request) && (!$this->isTagsMode($request) || !$this->isInputMode($request))) {
            return [];
        }

        $tags = $request->input(self::TAGS_FIELD_NAME);

        if ($request->input('mode_type') == self::INPUT_TYPE_NAME) {
            $position = $file->position;
            $tags = isset($tags[$position]) ? $tags[$position] : null;
        }

        $this->setTags($tags);
        $total = [];
        if ($tags != null) {
            foreach ($tags as $t) {
                $tag = new \App\SnapTag();
                $tag->name = $t['name'];
                $tag->total_price = $t['price'];
                $tag->weight = isset($t['weight']) ? $t['weight'] : null;
                $tag->quantity = $t['quantity'];
                $tag->img_x = isset($t['tag_x']) ? $t['tag_x'] : '';
                $tag->img_y = isset($t['tag_y']) ? $t['tag_y'] : '';
                $tag->current_signature = $this->generateSignature($tag->name, $tag->weight, $tag->quantity, $tag->total_price);
                $tag->file()->associate($file);

                $tag->save();
                $total[] = $t['price'];
            }
        }

        // add total value
        $this->totalValue($total, [], $file->id);

        return $tags;
    }

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return bool
     */
    private function hasMode(Request $request)
    {
        return $request->exists('mode_type') && $request->has('mode_type');
    }

    /**
     * @param \Illuminate\Http\Request $request
     *
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
     *
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
     *
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
     *
     * @return bool
     */
    private function isNoMode(Request $request)
    {
        if ($this->hasMode($request)) {
            return strtolower(self::NO_MODE_TYPE_NAME) === strtolower($request->input('mode_type')) ? true : false;
        }

        return false;
    }


    /**
     * @param \Illuminate\Http\Request $request
     *
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
     *
     * @return \Dusterio\PlainSqs\Jobs\DispatcherJob
     */
    protected function getPlainDispatcher($object)
    {
        return new DispatcherJob($object);
    }

    /**
     * @param $filename
     *
     * @return string
     */
    protected function completeImageLink($filename)
    {
        return $this->s3Url . '' . $filename;
    }

    /**
     * @param $array
     *
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
     *
     * @param $data
     *
     * @return bool
     */
    public function saveEstimatedPoint($data)
    {
        $requestCode = $data['request_code'];
        $memberId = $data['member_id'];
        $type = $data['type'];
        $mode = $data['mode'];
        $files = $data['files'];
        $tags = $data['tags'];
        $point = $data['point'];

        if ($mode == 'images') {
            $total = $point;
        } else {
            $total = $point * $files;
        }

        // $point = (new PointService)->calculateEstimatedPoint($memberId, $type, $mode, $tags);

        // if ($mode == 'tags' || $mode == 'input') {
        //     if ($tags == 0) {
        //         $total = $point['percent'];
        //     } else {
        //         $total = $point['point'] * $tags;
        //     }
        // } else {
        //     $total = $point['point'] * $files;
        // }

        $snap = (new SnapService)->getSnapByCode($requestCode);
        $snap->estimated_point = $total;
        $snap->update();

        $this->setEstimatedPoint($total);

        return $total;
    }

    private function setTags($value)
    {
        $this->countOfTags = $value;

        return $this;
    }

    private function getTags()
    {
        return $this->countOfTags;
    }

    private function setTotalValue($value)
    {
        return $this->totalValue = $value;
    }

    public function getTotalValue()
    {
        return $this->totalValue;
    }

    public function getCountOfTags($files)
    {
        $memberAdd = [];
        $crowdSourceEdit = [];
        $crowdSourceAdd = [];
        foreach ($files as $file) {
            $file = $this->getSnapFileById($file->id);
            $status = [];
            foreach ($file->tag as $tag) {
                $status[] = $this->checkTagStatus($tag->current_signature, $tag->edited_signature);
            }
            $count = array_count_values($status);
            $memberAdd[] = isset($count['member_add']) ? $count['member_add'] : 0;
            $crowdSourceEdit[] = isset($count['crowdsource_edit']) ? $count['crowdsource_edit'] : 0;
            $crowdSourceAdd[] = isset($count['crowdsource_add']) ? $count['crowdsource_add'] : 0;

        }

        $memberAdd = collect($memberAdd)->sum();
        $crowdSourceEdit = collect($crowdSourceEdit)->sum();
        $crowdSourceAdd = collect($crowdSourceAdd)->sum();

        $data = [
            'member_add' => $memberAdd,
            'crowdsource_edit' => $crowdSourceEdit,
            'crowdsource_add' => $crowdSourceAdd,
        ];

        return $data;
    }

    public function checkTagStatus($currentSignature, $editedSignature)
    {
        if ($currentSignature == null) {
            return 'crowdsource_add';
        } elseif ($currentSignature == $editedSignature || $editedSignature == null) {
            return 'member_add';
        } elseif ($currentSignature != $editedSignature) {
            return 'crowdsource_edit';
        }
    }

    private function setEstimatedPoint($value)
    {
        $this->estimatedPoint = $value;

        return $this;
    }

    public function getEstimatedPoint()
    {
        return $this->estimatedPoint;
    }

    public function getSnapToAssign()
    {
        return collect(Snap::whereNull('user_id')
                           ->get())->toArray();
    }

    public function assignToCrowdsource()
    {
        $config = config('common.queue_list.assign_process');
        $job = (new AssignJob())->onQueue($config)->onConnection(env('INFOSCAN_QUEUE'));
        dispatch($job);
    }

    public function getSnapByMemberCode($memberCode)
    {
        $member = Member::with('snap')
                        ->where('member_code', $memberCode)
                        ->first();

        return $member->snap;
    }

    public function getType($type)
    {
        if ($type == 'handWritten') {
            return 'Nota Tulis';
        } else {
            if ($type == 'generalTrade') {
                return 'Warung';
            } else {
                if ($type == 'receipt') {
                    return 'Struk';
                }
            }
        }
    }

    public function memberSnapHistory($snaps)
    {

        $point = $this->getEstimatedPoint();
        $snaps->comment = 'Kami sedang memproses foto transaksimu. Kamu bisa mendapatkan bonus poin sebesar ' . number_format($point, 0,0,'.') . ' poin!';
        $snaps->update();
    }

    public function getSnapByMemberId($memberId)
    {
        return Snap::with('files')
                   ->where('member_id', $memberId)
                   ->orderBy('created_at', 'DESC')
                   ->get();
    }

    public function sendSnapNotification($type, $point = '')
    {
        $message = config('common.notification_messages.snaps.' . $type);
        $sendMessage = sprintf("$message", (string)$point);

        (new NotificationService($sendMessage))->setData([
            'action' => 'history',
        ])->send();
    }

    public function sendSnapLimitNotification()
    {
        $sendMessage = 'Limit foto kamu sudah habis. Foto yang kamu kirim tidak akan diberikan points';

        (new NotificationService($sendMessage))->setData([
            'action' => 'notification',
        ])->send();
    }

    public function deleteTagsOrInput($request, $id)
    {
        $tags = $request->input('tag');
        $ids = $tags['id'];
        $ids = is_null($ids) ? [0] : $ids;

        $t = \DB::table('snaps')
                ->join('snap_files', 'snaps.id', '=', 'snap_files.snap_id')
                ->join('snap_tags', 'snap_files.id', '=', 'snap_tags.snap_file_id')
                ->where('snaps.id', $id)
                ->select('snap_tags.id')
                ->whereNotIn('snap_tags.id', $ids)
                ->get();

        $delete = [];
        foreach ($t as $tag) {
            $delete[] = $tag->id;
        }

        SnapTag::whereIn('id', $delete)->delete();
    }

    public function countMemberSnap($id, $type, $date, $nextDay = null)
    {
        return ($nextDay == null) ?
            Snap::where('snap_type', $type)
                ->where('member_id', $id)
                ->whereDate('created_at', $date)
                ->count() :
            Snap::where('snap_type', $type)
                ->where('member_id', $id)
                ->whereDate('created_at', '>=', $date)
                ->whereDate('created_at', '<=', $nextDay)
                ->count();
    }

    public function countMemberSnapFile($id, $type, $mode, $date, $nextDay = null)
    {
        return ($nextDay == null) ?
            \DB::table('snaps')
               ->join('snap_files', 'snaps.id', '=', 'snap_files.snap_id')
               ->where('snaps.member_id', $id)
               ->where('snaps.snap_type', $type)
               ->where('snaps.mode_type', $mode)
               ->whereDate('snaps.created_at', $date)
               ->where('snap_files.file_mimes', 'like', 'image%')
               ->count() :
            \DB::table('snaps')
               ->join('snap_files', 'snaps.id', '=', 'snap_files.snap_id')
               ->where('snaps.member_id', $id)
               ->where('snaps.snap_type', $type)
               ->where('snaps.mode_type', $mode)
               ->where('snap_files.file_mimes', 'like', 'image%')
               ->whereDate('snaps.created_at', '>=', $date)
               ->whereDate('snaps.created_at', '<=', $nextDay)
               ->count();
    }

    public function saveImageCrop($request)
    {
        $randomName = strtolower(str_random(10));
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = sprintf(
                "%s-%s.%s",
                $randomName,
                date('Ymdhis'),
                $file->getClientOriginalExtension()
            );

            $s3 = \Storage::disk('s3');
            $filePath = 'crops/' . $filename;
            $s3->put($filePath, file_get_contents($file), 'public');

            $link = 'crops/' . $filename;

            return $link;
        }
    }

    protected function getCodeTask($type, $mode)
    {
        if ($type == 'receipt') {
            $type = 'a';
        } elseif ($type == 'handWritten') {
            $type = 'b';
        } else {
            $type = 'c';
        }

        switch ($mode) {
            case 'audios':
                $mode = $type . '|5';
            break;

            case 'tags':
                $mode = $type . '|3';
            break;

            case 'input':
                $mode = $type . '|3';
            break;

            case 'image':
                $mode = $type;
            break;

            case 'images':
                $mode = $type;
            break;

            default:
                $mode = $type . '|1';
            break;
        }

        return $mode;
    }

    public function getTaskPointByCode($code, $type = null)
    {
        return ($type == 'receipt') ?
            \DB::table('tasks')
               ->join('task_points', 'tasks.id', '=', 'task_points.task_id')
               ->select('task_points.point as point')
               ->where('tasks.code', 'like', $code . '%')
               ->first() :
            \DB::table('tasks')
               ->join('task_points', 'tasks.id', '=', 'task_points.task_id')
               ->select('task_points.point as point')
               ->where('tasks.code', '=', $code)
               ->first();
    }

}
