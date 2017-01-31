<?php

namespace App\Http\Controllers\Web;

use App\Services\SnapService;
use App\Services\PointService;
use Illuminate\Http\Request;
use Exception;
use PDOException;

class SnapController extends AdminController
{

    public function index()
    {
        $this->isAllowed('Snaps.List');
        $snaps = (new SnapService)->getAvailableSnaps();
        $snapCategorys = config("common.snap_category");
        $snapCategoryModes = config("common.snap_category_mode");

        return view('snaps.index', compact('snaps', 'snapCategorys', 'snapCategoryModes'));
    }

    public function show(Request $request, $id)
    {
        $this->isAllowed('Snaps.Show');

        try {
            $snap = (new SnapService)->getSnapById($id);         
            if(null === $snap) {
                throw new Exception('Id Snap not valid!');
            }

            $paymentMethods = config("common.payment_methods");

            return view('snaps.show', compact('snap', 'paymentMethods'));
        } catch (Exception $e) {
            logger($e->getMessage());
            return view('errors.404');
        }           
        
    }

    public function filter($type, $mode)
    {
        $this->isAllowed('Snaps.List');

        $snaps = ( new SnapService);

        if ($type == 'all' && $mode == 'all') {
            $snaps = $snaps->getAvailableSnaps();            
        } else if($type == 'all') {
            $mode = config("common.snap_mode.$mode");
            $snaps = $snaps->getSnapsByMode($mode);
        } else if($mode == 'all') {
            $type = config("common.snap_type.$type");
            $snaps = $snaps->getSnapsByType($type);
        } else {
            $type = config("common.snap_type.$type");
            $mode = config("common.snap_mode.$mode");

            $snaps = $snaps->getSnapsByFilter($type, $mode);
        }

        return view('snaps.table_snaps', compact('snaps'));
    }

    public function edit($id)
    {
        $snap = (new SnapService)->getSnapByid($id);
        $snapFiles = $snap->files;
        $memberId = $snap->member_id;
        $snapType = $snap->snap_type;
        
        $files = [];
        foreach ($snapFiles as $snapFile) {
            $modeType = $snapFile->mode_type;
            $fileId = $snapFile->id;
            $calculate = (new PointService())
                ->calculatePoint($memberId, $snapType, $modeType, $fileId);
            $point = ($calculate != null) ? $calculate->point : '0';
            $files[] = [
                'filecode' => $snapFile->file_code,
                'mode' => $snapFile->mode_type,
                'point' => $point
            ];
        }   

        return view('snaps.edit', compact('snap', 'files'));
    }

    public function editSnapFile($id)
    {
        try {
            $modeView = [
                'input' => 'modal_inputs',
                'tags' => 'modal_tags',
                'audio' => 'modal_audios',
                'image' => 'modal_snaps'
            ];

            $snapFile = (new SnapService)->getSnapFileById($id);

            if(null === $snapFile->mode_type) {
                throw new \Exception('Mode type cannot be empty!');
            }
            $mode = $modeView[$snapFile->mode_type];

            return view("snaps.$mode", compact('snapFile'));
        } catch (\Exception $e) {
            logger($e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 404);
        }
    }

    public function snapDetail($id)
    {
        $snap = (new SnapService)->getSnapById($id);

        return view('snaps.show_detail', compact('snap'));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'tag.name.*' => 'required|max:255',
            'tag.qty.*' => 'required|max:11',
            'tag.total.*' => 'required|max:15',
            'newtag.name.*' => 'required|max:255',
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
            'longitude' => 'max:100',
            'latitude' => 'max:100',
        ]);

        try {
            if ($request->input('mode') === 'input') {
                (new SnapService)->updateSnapModeInput($request, $id);
            } else if ($request->input('mode') === 'tags') {
                (new SnapService)->updateSnapModeTags($request, $id);
            } else if ($request->input('mode') === 'audio') {
                (new SnapService)->updateSnapModeAudios($request, $id);
            } else if ($request->input('mode') === 'image') {
                (new SnapService)->updateSnapModeImages($request, $id);
            } else {
                (new SnapService)->updateSnap($request, $id);
            }

        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        } catch (PDOException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }

        $mode = ($request->has('mode') == true) ? $request->input('mode') : "";
        return response()->json([
            'status' => 'ok',
            'message' => 'Snaps '.$mode.' successfully updated!',
        ]);

    }

    public function tagging($id)
    {
        $rs = (new SnapService)->getSnapFileById($id);

        return response()->json($rs->tag);
    }

}
