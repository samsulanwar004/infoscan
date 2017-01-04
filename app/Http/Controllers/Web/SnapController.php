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
        $type = 'all';
        $snapCategorys = config("common.snap_category");
        $snapCategoryModes = config("common.snap_category_mode");

        return view('snaps.index', compact('snaps', 'type', 'snapCategorys', 'snapCategoryModes'));
    }

    public function show(Request $request, $id)
    {
        $this->isAllowed('Snaps.Show');
        $snap = (new SnapService)->getSnapById($id);

        return view('snaps.show', compact('snap'));
    }

    public function filter($attr)
    {
        $this->isAllowed('Snaps.List');

        $snaps = ( new SnapService);

        if (config("common.snap_type.$attr")) {
            $att = config("common.snap_type.$attr");
            $snaps = $snaps->getSnapsByType($att);
            $type = $attr;
        } else {
            $att = config("common.snap_mode.$attr");
            $snaps = $snaps->getSnapsByMode($att);
            $type = $attr;
        }

        $snapCategorys = config("common.snap_category");
        $snapCategoryModes = config("common.snap_category_mode");

        return view('snaps.index', compact('snaps', 'type', 'snapCategorys', 'snapCategoryModes'));
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
            'audio' => 'modal_audios'
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
            if ($id === 'input') {
                (new SnapService)->updateSnapModeInput($request);
            } else if ($id === 'tags') {
                (new SnapService)->updateSnapModeTags($request);
            } else if ($id === 'audio') {

            } else {
                (new SnapService)->updateSnap($request, $id);
            }
            
        } catch (Exception $e) {
            return redirect()->back()->withInput()->withErrors($e->getMessage());
        } catch (PDOException $e) {
            return redirect()->back()->withInput()->withErrors($e->getMessage());
        }

        return redirect()->back()->with('success', 'Snaps successfully updated!');
    }

    public function tagging($id)
    {
        $rs = (new SnapService)->getSnapFileById($id);

        return response()->json($rs->tag);
    }

}
