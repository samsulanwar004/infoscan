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

        if (config("common.snap_type.$attr"))
        {
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
        $this->isAllowed('Points.Update');
        $snapFile = (new SnapService)->getSnapFileById($id);
        return view('snaps.edit', compact('snapFile'));
    }

    public function update(Request $request, $id)
    {
        try {
            if ($id === 'tag')
            {
                (new SnapService)->updateSnapTags($request);
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

    public function updateTag(Request $request)
    {
        try {
            (new SnapService)->updateSnapTags($request);
        } catch (Exception $e) {
            return redirect()->back()->withInput()->withErrors($e->getMessage());
        } catch (PDOException $e) {
            return redirect()->back()->withInput()->withErrors($e->getMessage());
        }

        return redirect()->back()->with('success', 'Tags successfully updated!');
    }

}
