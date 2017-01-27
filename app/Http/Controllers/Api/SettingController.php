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
	        $data['monthly_expenses'] = [
	        	['min' => 0, 'max' => 1000000],
	        	['min' => 1000000, 'max' => 3000000],
	        	['min' => 3000000, 'max' => 5000000],
	        	['min' => 5000000, 'max' => 10000000],
	        ];
	        $data['toc'] = 'Lorem ipsum dolor sit amet aja dah mah';
	        $data['banks'] = $commonConfigs['banks'];
	        $data['latest_educations'] = $commonConfigs['latest_educations'];

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
}
