<?php

namespace App\Http\Controllers\Api;

use App\Services\LuckyDrawService;
use Illuminate\Http\Request;
use Exception;
use Validator;

class LuckyDrawController extends BaseApiController 
{
	public function index()
	{
		try {

			$lucky = (new LuckyDrawService)->getApiAllLuckyDraw();
			
			return $this->success($lucky, 200);
		} catch (Exception $e) {
			return $this->error($e, 400, true);
		}
	}

	public function store(Request $request)
	{
		try {

			$validation = $this->validRequest($request);
            if ($validation->fails()) {
                return $this->error($validation->errors(), 400, true);
            }

			$number = (new LuckyDrawService)->getRandomNumber($request);

			return $this->success($number, 200);
			
		} catch (Exception $e) {
            return $this->error($e, 400, true);
		}
	}

	/**
     * Validate the user request input data.
     *
     * @param  Request $request
     * @return \Illuminate\Validation\Validator
     */
    private function validRequest(Request $request)
    {

        $rules = [
            'code' => 'required|size:10',
            'point' => 'required',
        ];

        return Validator::make($request->all(), $rules);
    }

}