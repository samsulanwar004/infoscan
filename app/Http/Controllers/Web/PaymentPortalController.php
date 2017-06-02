<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use App\Services\PaymentService;

class PaymentPortalController extends AdminController
{
    public function index()
    {
        $this->isAllowed('PaymentPortal.List');
        $date = false;
    	$payment = (new PaymentService)->getListPaymentPortal();

    	return view('payment.index', compact('payment', 'date'));
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

    public function export(Request $request)
    {
        try {
            if ($request->all() == false) {
                return view('errors.404');
            } else if ($download = $request->input('download')) {
                $get = storage_path('csv/'.$download);
                return response()->download($get)->deleteFileAfterSend(true);;
            }

            $dateStart = $request->input('date_start');
            $dateEnd = $request->input('date_end');
            $typeRequest = $request->input('type_request');
            $filename = $request->input('filename');
            $page = $request->input('page');
            $no = $request->input('no');

            $data = [
                'start_date' => $dateStart,
                'end_date' => $dateEnd,
                'filename' => $filename,
                'type' => $typeRequest,
                'page' => $page,
                'no' => $no,
            ];

            $payment = (new PaymentService)->getExportPaymentToCsv($data);

            return response()->json([
                'status' => 'ok',
                'message' => $payment,
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage().' '.$e->getLine(),
            ], 500);
        }
    }
}
