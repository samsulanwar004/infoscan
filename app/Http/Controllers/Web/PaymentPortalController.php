<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use App\Services\PaymentService;

class PaymentPortalController extends AdminController
{
    public function index()
    {
        $this->isAllowed('PaymentPortal.List');
    	$payment = (new PaymentService)->getListPaymentPortal();

    	return view('payment.index', compact('payment'));
    }

    public function edit($id)
    {
    	try {
    		$payment = (new PaymentService)->getPaymentPortalById($id);

    		return view('payment.edit', compact('payment'));
    	} catch (\Exception $e) {
    		return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 404);
    	}
    }

    public function update(Request $request, $id)
    {
    	try {
    		(new PaymentService)->saveConfirmation($request, $id);
    	} catch (\Exception $e) {
    		return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 404);
    	}

    	return response()->json([
            'status' => 'ok',
            'message' => 'Confirmation successfully!',
        ]);
    }
}
