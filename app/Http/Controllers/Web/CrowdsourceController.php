<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use App\Services\CrowdsourceService;
use App\Services\SnapService;

class CrowdsourceController extends AdminController
{	

    public function index()
    {
        $this->isAllowed('Crowdsource.List');
        $csService = (new CrowdsourceService);
        $crowdsources = $csService->getAllCrowdsource();
        $remainAssign = $csService->getRemainAssignSnap();

    	return view('crowdsource.index', compact('crowdsources', 'remainAssign'));
    }

    public function show(Request $request, $id)
    {
        $this->isAllowed('Crowdsource.List');
        $crowdsource = (new CrowdsourceService);
    	$date = false;
        if ($request->has('start_at') && $request->has('end_at')) {
            
            $dateStartArr = explode('-', $request->input('start_at'));
            $dateEndArr = explode('-', $request->input('end_at'));
            $date = $dateStartArr[1].'/'.$dateStartArr[2].'/'.$dateStartArr[0].' - '.$dateEndArr[1].'/'.$dateEndArr[2].'/'.$dateEndArr[0];

            $activities = $crowdsource->getCrowdsourceActivityByFilter($request, $id)
                ->appends('start_at', $request->input('start_at'))
                ->appends('end_at', $request->input('end_at'));
        } else {
            $activities = $crowdsource->getCrowdsourceActivityByUserId($id);
        }
        $data = $crowdsource->getCalculateCrowdsource($activities);

        $user = $crowdsource->getSnapByUserId($id);

        $assign = count($user->snaps);

    	return view('crowdsource.show', compact('activities', 'data', 'id', 'assign', 'date'));
    }

    public function detailActivity($id)
    {
        $this->isAllowed('Crowdsource.List');
    	$crowdsourceActivity = (new CrowdsourceService)->getCrowdsourceActivityById($id);
		$extract = json_decode($crowdsourceActivity->description);
		$snapId = $extract->data->snap_id;
    	$snap = (new SnapService)->getSnapById($snapId);

    	return view('crowdsource.show_detail', compact('crowdsourceActivity','snap', 'extract'));
    }
}
