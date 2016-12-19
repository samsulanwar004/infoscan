<?php

namespace App\Http\Controllers\Web;

use App\Services\SnapService;
use Illuminate\Http\Request;

class SnapController extends AdminController
{
    public function index()
    {
        $snaps = (new SnapService)->getAvailableSnaps();

        return view('snaps.index', compact('snaps'));
    }

    public function show(Request $request, $id)
    {
    	$snap = (new SnapService)->getSnapById($id);

    	return view('snaps.show', compact('snap'));
    }

}
