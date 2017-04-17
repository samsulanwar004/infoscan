<?php

namespace App\Http\Controllers\Web;

use App\Services\SnapService;
use App\Services\PointService;
use Illuminate\Http\Request;
use Exception;
use PDOException;

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
            $files = $snap->files;

            $audioFiles = $files->filter(function($value, $Key) {
                return starts_with($value->file_mimes, 'audio');
            });

            $audios = [];
            foreach ($audioFiles as $audio) {
                $audios[] = $audio;
            }

            $paymentMethods = config("common.payment_methods");

            return view('snaps.show', compact('snap', 'paymentMethods', 'audios'));
        } catch (Exception $e) {
            logger($e->getMessage());
            return view('errors.404');
        }

    }

    public function edit($id)
    {
        try {
            $snap = (new SnapService)->getSnapByid($id);

            $fixedPoint = (new PointService)->calculateApprovePoint($snap);

            return view('snaps.edit', compact('snap', 'fixedPoint'));
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
            'tag.name.*' => 'required|max:255',
            'tag.weight.*' => 'required|max:255',
            'tag.qty.*' => 'required|max:11',
            'tag.total.*' => 'required|max:15',
            'newtag.name.*' => 'required|max:255',
            'newtag.weight.*' => 'required|max:255',
            'newtag.qty.*' => 'required|max:11',
            'newtag.total.*' => 'required|max:15',
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
            if ($request->input('mode') === 'input') {
                (new SnapService)->updateSnapModeTags($request, $id);
            } else if ($request->input('mode') === 'tags') {
                (new SnapService)->updateSnapModeTags($request, $id);
            } else if ($request->input('mode') === 'no_mode') {
                (new SnapService)->updateSnapModeTags($request, $id);
            } else if ($request->input('mode') === 'audios') {
                (new SnapService)->updateSnapModeAudios($request, $id);
            } else if ($request->input('mode') === 'image') {
                (new SnapService)->updateSnapModeImages($request, $id);
            }else if ($request->input('mode') === 'confirm') {
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

}
