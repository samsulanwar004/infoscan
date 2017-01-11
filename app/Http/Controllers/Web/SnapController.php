<?php

namespace App\Http\Controllers\Web;

use App\Services\SnapService;
use Illuminate\Http\Request;
use Exception;
use PDOException;

class SnapController extends AdminController
{

    public function index()
    {
        $this->isAllowed('Snaps.List');
        $snaps = ( new SnapService)->getAvailableSnaps();
        $snapCategorys = config("common.snap_category");
        $snapCategoryModes = config("common.snap_category_mode");

        return view('snaps.index', compact('snaps', 'snapCategorys', 'snapCategoryModes'));
    }

    public function show(Request $request, $id)
    {
        $this->isAllowed('Snaps.Show');
        $snap = (new SnapService)->getSnapById($id);

        return view('snaps.show', compact('snap'));
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

        return view('snaps.edit', compact('snap'));
    }

    public function editSnapFile($id)
    {
        $modeView = [
            'input' => 'modal_inputs',
            'tags' => 'modal_tags',
            'audio' => 'modal_audios',
            'image' => 'modal_snaps'
        ];

        $snapFile = (new SnapService)->getSnapFileById($id);
        $mode = $modeView[$snapFile->mode_type];
        return view("snaps.$mode", compact('snapFile'));
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

        $mode = ($request->has('mode') == true) ? $request->input('mode') : "Confirmation";
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
