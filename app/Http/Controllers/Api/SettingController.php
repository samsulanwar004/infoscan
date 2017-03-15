<?php

namespace App\Http\Controllers\Api;

class SettingController extends BaseApiController
{
    /**
     * Get all of settings.
     *  - provinces, monthly_expenses, toc, banks, latest_educations
     *
     * @return \Illiminate\Http\Response
     */
    public function index()
    {
    	$commonConfigs = config('common');

        try {
        	$data['provinces'] = $this->provinceData();
	        $data['monthly_expenses'] = $this->sesData();
	        $data['toc'] = 'Lorem ipsum dolor sit amet aja dah mah';
	        $data['banks'] = $commonConfigs['banks'];
	        $data['latest_educations'] = $commonConfigs['latest_educations'];
            $data['genders'] = $commonConfigs['genders'];
            $data['marital_statuses'] = $commonConfigs['marital_statuses'];

	        return $this->success($data, 200);
        } catch (\Exception $e) {
        	return $this->error($e);
        }
    }

    private function provinceData()
    {
    	$p = collect(\App\Province::all(['id', 'name'])->toArray());
    	//$plucked = $p->pluck('name', 'id');

    	return $p;
    }

    private function sesData()
    {
        $ses = \App\SocioEconomicStatus::orderBy('range_start')
            ->select('range_start as min', 'range_end as max')
            ->get();
        $s = collect($ses)->toArray();

        return $s;
    }
}
