<?php

namespace App\Services;

use App\User;
use App\CrowdsourceActivity;
use DB;

class CrowdsourceService
{

	const NAME_ROLE = 'Crowdsource Account';

	public function getActivityById($id)
	{
		
		$user = $this->getCrowdsourceById($id);

    	$action = [];
    	$addTag = [];
    	$editTag = [];

    	foreach ($user->crowdsourceActivities as $activities) {
    		$extract = json_decode($activities->description);
    		$action[] =  $extract->action;
    		
    		if ($extract->action == 'update') {
    			$addTag[] = $extract->data->add_tag;
    			$editTag[] = $extract->data->edit_tag;
    		}
    	}
    	
    	$action = array_count_values($action);

    	$totalAddTag = collect($addTag)->sum();
    	$totalEditTag = collect($editTag)->sum();  	
    	$totalUpdate = isset($action['update']) ? $action['update'] : 0;
    	$totalApprove = isset($action['approve']) ? $action['approve'] : 0;
    	$totalReject = isset($action['reject']) ? $action['reject'] : 0;

    	$data = [
    		'totalAddTag' => $totalAddTag,
    		'totalEditTag' => $totalEditTag,
    		'totalUpdate' => $totalUpdate,
    		'totalApprove' => $totalApprove,
    		'totalReject' => $totalReject,
    	];

    	return $data;
	}

	public function getCrowdsourceById($id)
	{
		return User::with('crowdsourceActivities')
    		->where('id', $id)
    		->first();
	}

	public function getAllCrowdsource()
	{
		return DB::table('users')
        	->join('users_roles', 'users.id', '=', 'users_roles.user_id')
        	->join('roles', 'roles.id', '=', 'users_roles.role_id')
        	->select('users.id', 'users.name', 'users.email', 'users.is_active', 'roles.role_name')
        	->where('roles.role_name', '=', self::NAME_ROLE)
        	->get();
	}

	public function getCrowdsourceActivityByUserId($id)
	{
		$crowdsource = $this->getCrowdsourceById($id);

		return $crowdsource->crowdsourceActivities;
	}

	public function getCrowdsourceActivityById($id)
	{
		return CrowdsourceActivity::where('id', $id)->first();
	}

}