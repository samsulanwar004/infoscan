<?php

namespace App\Services;

use App\User;
use App\CrowdsourceActivity;
use DB;
use App\Snap;
use Rebel\Component\Rbac\Models\Role;

class CrowdsourceService
{

	const NAME_ROLE = 'Crowdsource Account';

	public function getCrowdsourceById($id)
	{
		return User::with('crowdsourceActivities')
    		->where('id', $id)
    		->first();
	}

	public function getAllCrowdsource()
	{
        $crowdsource = Role::with('users')
            ->where('role_name', '=', self::NAME_ROLE)
            ->first();

        return $crowdsource->users;
	}

	public function getCrowdsourceActivityByUserId($id)
	{
		$crowdsource = $this->getCrowdsourceById($id);

		return $crowdsource->crowdsourceActivities()->paginate(50);
	}

	public function getCrowdsourceActivityById($id)
	{
		return CrowdsourceActivity::where('id', $id)->first();
	}

    public function getCrowdsourceActivityByFilter($request, $id)
    {
        $crowdsource = $this->getCrowdsourceById($id);

        $startAt = $request->input('start_at');
        $endAt = $request->input('end_at');

        return $crowdsource->crowdsourceActivities()
            ->whereDate('created_at', '>=', $startAt)
            ->whereDate('created_at', '<=', $endAt)
            ->paginate(50);
    }

    public function getCalculateCrowdsource($activities)
    {

        $action = [];
        $addTag = [];
        $editTag = [];

        foreach ($activities as $activity) {
            $extract = json_decode($activity->description);
            $action[] =  $extract->action;
            $addTag[] = $extract->data->add_tag;
            $editTag[] = $extract->data->edit_tag;
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
            'totalApprove' => $totalApprove,
            'totalReject' => $totalReject,
        ];

        return $data;
    }

    public function getCrowdsourceToAssign()
    {
        return collect(DB::table('users')
            ->join('users_roles', 'users.id', '=', 'users_roles.user_id')
            ->join('roles', 'roles.id', '=', 'users_roles.role_id')
            ->select('users.id')
            ->where('roles.role_name', '=', self::NAME_ROLE)
            ->get())->toArray();
    }

    public function getSnapByFilter($request, $id)
    {
        $start = $request->input('start_at');
        $end = $request->input('end_at');

        $user = $this->getSnapByUserId($id);

        $snaps = $user->snaps->filter(function($value, $Key) use ($start, $end) {
            return $value->created_at->format('Y-m-d') >= $start &&
                    $value->created_at->format('Y-m-d') <= $end;
        });

        return $snaps;
    }

    public function getSnapByUserId($id)
    {
        return User::with('snaps')
            ->where('id', $id)
            ->first();
    }

    public function getRemainAssignSnap()
    {
        return DB::table('snaps')
             ->select(DB::raw('count(*) as remain, user_id'))
             ->where('status', 'pending')
             ->groupBy('user_id')
             ->get();
    }

}