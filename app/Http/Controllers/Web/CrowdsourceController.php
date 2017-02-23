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
        $crowdsources = (new CrowdsourceService)->getAllCrowdsource();

    	return view('crowdsource.index', compact('crowdsources'));
    }

    public function show($id)
    {
        $this->isAllowed('Crowdsource.List');
    	$activities = (new CrowdsourceService)->getCrowdsourceActivityByUserId($id);

    	return view('crowdsource.show', compact('activities', 'id'));
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

    public function dateFilter(Request $request)
    {
        $id = $request->input('id');
    	$start = $request->input('start_at');
    	$end = $request->input('end_at');

    	$activities = (new CrowdsourceService)->getCrowdsourceActivityByUserId($id);

        $activities = $activities->filter(function($value, $Key) use ($start, $end) {
            return $value->created_at->format('Y-m-d') >= $start &&
                    $value->created_at->format('Y-m-d') <= $end;
        });

    	return view('crowdsource.table_activity', compact('activities'));
    }
}
