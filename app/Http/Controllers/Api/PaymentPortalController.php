<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Services\PaymentService;
use Validator;

class PaymentPortalController extends BaseApiController
{
    public function index()
    {
    	try {
    		$payment = (new PaymentService)->getPaymentPortal();
			
			return $this->success($payment, 200);
    	} catch (\Exception $e) {
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

            (new PaymentService)->redeemPointToCash($request);

            return $this->success();
    	} catch (\Exception $e) {
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
            'point' => 'required|numeric',
            'bank_account' => 'required',
            'account_number' => 'required',
        ];

        return Validator::make($request->all(), $rules);
    }
}
