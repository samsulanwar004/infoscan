<?php

namespace App\Http\Controllers\Web;

use App\Services\SnapService;
use App\Services\PointService;
use Illuminate\Http\Request;
use Exception;
use PDOException;
use App\Setting;

class SnapController extends AdminController
{

    const NAME_ROLE = 'Crowdsource Account';

    public function index()
    {
        $this->isAllowed('Snaps.List');
        $user = auth('web')->user();
        $snaps = ( new SnapService);

        $date = false;
        $status = false;
        $type = false;
        $mode = false;
        $search = false;
        $admin = $this->isSuperAdministrator();

        if (request()->has('date_start') && request()->has('date_end')) {
            $dateStart = request()->input('date_start');
            $dateEnd = request()->input('date_end');
            $type = request()->input('type');
            $typeFilter = config("common.snap_type.$type");
            $mode = request()->input('mode');
            $modeFilter = config("common.snap_mode.$mode");
            $status = request()->input('status');
            $search = request()->input('search');

            $data = [
                'start_date' => $dateStart,
                'end_date' => $dateEnd,
                'snap_type' => $typeFilter,
                'mode_type' => $modeFilter,
                'status' => $status,
                'search' => $search,
            ];
            
            $dateStartArr = explode('-', $dateStart);
            $dateEndArr = explode('-', $dateEnd);
            $date = $dateStartArr[1].'/'.$dateStartArr[2].'/'.$dateStartArr[0].' - '.$dateEndArr[1].'/'.$dateEndArr[2].'/'.$dateEndArr[0];

            $status = $status;

            if ($user->roles[0]->role_name == self::NAME_ROLE) {
                $id = $user->id;
                $snaps = $snaps->getSnapsByFilter($data, $id)
                    ->appends('date_start', $dateStart)
                    ->appends('date_end', $dateEnd)
                    ->appends('status', $status)
                    ->appends('type', $type)
                    ->appends('mode', $mode)
                    ->appends('search', $search);
            } else {
                $snaps = $snaps->getSnapsByFilter($data)
                    ->appends('date_start', $dateStart)
                    ->appends('date_end', $dateEnd)
                    ->appends('status', $status)
                    ->appends('type', $type)
                    ->appends('mode', $mode)
                    ->appends('search', $search);
            }
        } else if (request()->has('search')) {
            $search = request()->input('search');

            if ($user->roles[0]->role_name == self::NAME_ROLE) {
                $id = $user->id;
                $snaps = $snaps->getSnapsBySearch($search, $id)
                    ->appends('search', $search);
            } else {
                $snaps = $snaps->getSnapsBySearch($search)
                    ->appends('search', $search);
            }
        } else {
            if ($user->roles[0]->role_name == self::NAME_ROLE) {
                $id = $user->id;
                $snaps = $snaps->getAvailableSnapsByUser($id);
            } else {
                $snaps = $snaps->getAvailableSnaps();
            }
        }

        $snapCategorys = config("common.snap_category");
        $snapCategoryModes = config("common.snap_category_mode");

        return view('snaps.index', compact('snaps', 'snapCategorys', 'snapCategoryModes', 'date', 'status', 'type', 'mode', 'search', 'admin'));
    }

    public function show(Request $request, $id)
    {
        $this->isAllowed('Snaps.Show');

        try {
            $snap = (new SnapService)->getSnapById($id);
            if(null === $snap) {
                throw new Exception('Id Snap not valid!');
            }      
            // build code for approve
            $code = $this->getCodeTask($snap->snap_type, $snap->mode_type);

            $files = $snap->files()->where('file_mimes', 'like', 'image%')->paginate(1);

            $audios = $snap->files->filter(function($value, $Key) use ($files){
                return starts_with($value->file_mimes, 'audio') && $value->position == $files->first()->position;
            })->first();

            $paymentMethods = config("common.payment_methods");

            return view('snaps.show', compact('snap', 'paymentMethods', 'audios', 'files', 'code'));
        } catch (Exception $e) {
            logger($e->getMessage());
            return view('errors.404');
        }

    }

    public function edit($id)
    {
        try {
            $snap = (new SnapService)->getSnapByid($id);

            $point = [];
            foreach ($snap->files as $file) {
                $point[] = $file->image_point;
            }
            $fixedPoint = collect($point)->sum();
            $point = (new PointService)->calculatePromoPoint($snap->member_id, $snap->outlet_city);

            $promo = $point['point_city'] + $point['point_level_city'];
            $reasons = Setting::where('setting_group', 'snap_reason')
                ->get();

            $admin = $this->isSuperAdministrator();

            return view('snaps.edit', compact('snap', 'fixedPoint', 'promo', 'reasons', 'admin'));
        } catch (\Exception $e) {
            logger($e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 404);
        }

    }

    public function editSnapFile($id)
    {
        try {
            $modeView = [
                'input' => 'modal_tags',
                'tags' => 'modal_tags',
                'audios' => 'modal_audios',
                'image' => 'modal_snaps',
                'no_mode' => 'modal_tags',
            ];

            $snapFile = (new SnapService)->getSnapFileById($id);

            $view = null === $snapFile->mode_type ? 'image' : $snapFile->mode_type;
            $mode = $modeView[$snapFile->mode_type];

            $audioFile = null;
            if ($snapFile->mode_type == 'audios')
            {
                $snapId = $snapFile->snap_id;
                $position = $snapFile->position;

                $audioFile = (new SnapService)->getSnapFileByPosition($snapId, $position);

            }

            return view("snaps.$mode", compact('snapFile', 'audioFile'));
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 404);
        }
    }

    public function snapDetail($id)
    {
        $snap = (new SnapService)->getSnapById($id);
        $js = 'ok';

        return view('snaps.show_detail', compact('snap', 'js'));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'comment' => 'max:100',
            'outlet_name' => 'max:100',
            'location' => 'max:255',
            'receipt_id' => 'max:100',
            'outlet_type' => 'max:100',
            'outlet_city' => 'max:100',
            'outlet_province' => 'max:100',
            'outlet_zip_code' => 'max:100',
            'outlet_rt_rw' => 'max:100',
            'longitude' => 'max:100',
            'latitude' => 'max:100',
        ]);

        try {
            if ($request->input('mode') === 'confirm') {
                (new SnapService)->confirmSnap($request, $id);
            } else {
                $snap = (new SnapService);
                $snap->updateSnap($request, $id);
                $totalValue = $snap->getTotalValue();
            }

        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine(),
            ], 500);
        } catch (PDOException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine(),
            ], 500);
        }

        $mode = ($request->has('mode') == true) ? $request->input('mode') : "";
        return response()->json([
            'status' => 'ok',
            'data' => isset($totalValue) ? clean_numeric($totalValue,'%',false,'.') : '',
            'message' => 'Snaps '.$mode.' successfully updated!',
        ]);

    }

    public function tagging($id)
    {
        $rs = (new SnapService)->getSnapFileById($id);

        return response()->json($rs->tag);
    }

    public function export(Request $request)
    {

        try {
            if ($request->all() == false) {
                return view('errors.404');
            } else if ($download = $request->input('download')) {
                $get = storage_path('csv/'.$download);
                return response()->download($get)->deleteFileAfterSend(true);;
            }

            $dateStart = $request->input('date_start');
            $dateEnd = $request->input('date_end');
            $type = $request->input('type');
            $typeFilter = config("common.snap_type.$type");
            $mode = $request->input('mode');
            $modeFilter = config("common.snap_mode.$mode");
            $status = $request->input('status');
            $search = $request->input('search');
            $typeRequest = $request->input('type_request');
            $filename = $request->input('filename');
            $page = $request->input('page');
            $no = $request->input('no');

            $data = [
                'start_date' => $dateStart,
                'end_date' => $dateEnd,
                'snap_type' => $typeFilter,
                'mode_type' => $modeFilter,
                'status' => $status,
                'search' => $search,
                'type' => $typeRequest,
                'filename' => $filename,
                'page' => $page,
                'no' => $no,
            ];

            $snaps = (new SnapService)->getExportSnapToCsv($data);

            return response()->json([
                'status' => 'ok',
                'message' => $snaps,
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage().' '.$e->getLine(),
            ], 500);
        }
    }

    public function taggingSave(Request $request)
    {
        try {
            $t = new \App\SnapTag;
            $t->name = $request->input('name');
            $t->weight = $request->input('weight');
            $t->quantity = $request->input('quantity');
            $t->total_price = $request->input('total_price');
            $t->img_x = $request->input('img_x');
            $t->img_y = $request->input('img_y');
            $t->file()->associate($request->input('file_id'));

            $t->save();

            return response()->json([
                'status' => 'ok',
                'message' => $t->id,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function cropUpload(Request $request)
    {
        try {
            $image = (new SnapService)->saveImageCrop($request);

            return response()->json([
                'status' => 'ok',
                'message' => $image,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ]);
        }
    }

    protected function getCodeTask($type, $mode)
    {
        if($type == 'receipt') {
            $type = 'a';
        } elseif ($type == 'handWritten') {
            $type = 'b';
        } else {
            $type = 'c';
        }

        switch ($mode) {
            case 'audios':
                $mode = array('Only Pic' => $type.'|1','Error' =>$type.'|4', 'Error Free' =>$type.'|5');
                break;

            case 'tags':
                $mode = array('Only Pic' => $type.'|1','Error' => $type.'|2', 'Error Free' =>$type.'|3');
                break;

            case 'input':
                $mode = array('Only Pic' => $type.'|1','Error' => $type.'|2', 'Error Free' => $type.'|3');
                break;
            
            default:
                $mode = array('Only Pic' => $type.'|1');
                break;
        }

        return $mode;
    }

    public function approveImage(Request $request, $id)
    {
        $this->validate($request, [
            'tag.name.*' => 'required|max:255',
            'tag.weight.*' => 'required|max:255',
            'tag.qty.*' => 'required|max:11',
            'tag.total.*' => 'required|max:15',
            'newtag.name.*' => 'required|max:255',
            'newtag.weight.*' => 'required|max:255',
            'newtag.qty.*' => 'required|max:11',
            'newtag.total.*' => 'required|max:15',
        ]);

        try {
            if ($request->input('mode_type') === 'input') {
                (new SnapService)->updateSnapModeTags($request, $id);
            } else if ($request->input('mode_type') === 'tags') {
                (new SnapService)->updateSnapModeTags($request, $id);
            } else if ($request->input('mode_type') === 'no_mode') {
                (new SnapService)->updateSnapModeTags($request, $id);
            } else if ($request->input('mode_type') === 'audios') {
                (new SnapService)->updateSnapModeAudios($request, $id);
            } else if ($request->input('mode_type') === 'image') {
                (new SnapService)->updateSnapModeImages($request, $id);
            }            
            
            $file = (new SnapService)->getSnapFileById($id);
            if ($request->input('image_approve') == 'reject') {
                $file->is_approve = 0;
                $file->image_point = 0;
            } else {

                if ($file->mode_type == 'image') {
                    $tag = $request->input('tag');
                    $newtag = $request->input('newtag');
                    $totalTag = count($tag['name']) + count($newtag['name']);
                    $code = $request->input('image_approve');
                    $task = $this->getTaskPointByRange($code, $totalTag);

                    $file->image_code = isset($task->code) ? $task->code : null;
                    $file->image_point = isset($task->point) ? $task->point : 0;

                    $file->is_approve = 1;
                } else {
                    $code = $request->input('image_approve');
                    $task = (new SnapService)->getTaskPointByCode($code);
                    $file->image_code = $code;
                    $file->image_point = isset($task->point) ? $task->point : 0;

                    $file->is_approve = 1;
                }
            }

            $file->update();

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'status' => 'ok',
            'message' => 'Approve this image successfully',
        ]);
    }

    private function getTaskPointByRange($code, $count)
    {
        return \DB::table('tasks')
            ->join('task_points', 'tasks.id', '=', 'task_points.task_id')
            ->select('task_points.point as point', 'tasks.code as code')
            ->where('tasks.code', 'like', $code.'%')
            ->where('task_points.range_start', '<=', $count)
            ->where('task_points.range_end', '>=', $count)
            ->first();
    }

}
