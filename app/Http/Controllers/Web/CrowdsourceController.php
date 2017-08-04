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

        $snapsCs = ( new CrowdsourceService);

        $date = false;
        $status = false;
        $type = false;
        $mode = false;
        $search = false;
        $admin = $this->isSuperAdministrator();
        $userId = $id;

        if ($request->has('date_start') && $request->has('date_end')) {
            $dateStart = $request->input('date_start');
            $dateEnd = $request->input('date_end');
            $type = $request->input('type');
            $typeFilter = config("common.snap_type.$type");
            $mode = $request->input('mode');
            $modeFilter = config("common.snap_mode.$mode");
            $status = $request->input('status');

            $data = [
                'start_date' => $dateStart,
                'end_date' => $dateEnd,
                'snap_type' => $typeFilter,
                'mode_type' => $modeFilter,
                'status' => $status,
            ];

            $dateStartArr = explode('-', $dateStart);
            $dateEndArr = explode('-', $dateEnd);
            $date = $dateStartArr[1].'/'.$dateStartArr[2].'/'.$dateStartArr[0].' - '.$dateEndArr[1].'/'.$dateEndArr[2].'/'.$dateEndArr[0];

            $status = $status;

            $snaps = $snapsCs->getSnapsCsByFilter($data, $id)
                ->appends('date_start', $dateStart)
                ->appends('date_end', $dateEnd)
                ->appends('status', $status)
                ->appends('type', $type)
                ->appends('mode', $mode);
        } else {
            $snaps = $snapsCs->getSnapsCs($id);
        }

        $snapCategorys = config("common.snap_category");
        $snapCategoryModes = config("common.snap_category_mode");

        return view('crowdsource.show', compact('snaps', 'snapCategorys', 'snapCategoryModes', 'date', 'status', 'type', 'mode', 'userId', 'admin'));
    }

    // public function show(Request $request, $id)
    // {
    //     $this->isAllowed('Crowdsource.List');
    //     $crowdsource = (new CrowdsourceService);
    // 	$date = false;
    //     if ($request->has('start_at') && $request->has('end_at')) {

    //         $dateStartArr = explode('-', $request->input('start_at'));
    //         $dateEndArr = explode('-', $request->input('end_at'));
    //         $date = $dateStartArr[1].'/'.$dateStartArr[2].'/'.$dateStartArr[0].' - '.$dateEndArr[1].'/'.$dateEndArr[2].'/'.$dateEndArr[0];

    //         $activities = $crowdsource->getCrowdsourceActivityByFilter($request, $id)
    //             ->appends('start_at', $request->input('start_at'))
    //             ->appends('end_at', $request->input('end_at'));
    //     } else {
    //         $activities = $crowdsource->getCrowdsourceActivityByUserId($id);
    //     }
    //     $data = $crowdsource->getCalculateCrowdsource($activities);

    //     $user = $crowdsource->getSnapByUserId($id);

    //     $assign = count($user->snaps);

    // 	return view('crowdsource.show', compact('activities', 'data', 'id', 'assign', 'date'));
    // }

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
