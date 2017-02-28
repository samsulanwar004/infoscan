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
        $crowdsource = (new CrowdsourceService);
    	$activities = $crowdsource->getCrowdsourceActivityByUserId($id);
        $data = $crowdsource->getCalculateCrowdsource($activities);

    	return view('crowdsource.show', compact('activities', 'data', 'id'));
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
        $crowdsource = (new CrowdsourceService);
        $activities = $crowdsource->getCrowdsourceActivityByFilter($request);

        $data = $crowdsource->getCalculateCrowdsource($activities);

    	return view('crowdsource.table_activity', compact('activities', 'data', 'id'));
    }
}
