<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use App\Services\ReportService;

class ReportController extends AdminController
{
	public function index(Request $request)
	{
		$results = [];
		return view('reports.index', compact('results'));
	}

	public function createChart(Request $request)
	{
		try {
			$reports = (new ReportService);
			$chart = $reports->buildChart($request);
			$size = $reports->getSize();
			$type = $chart['type'];
			$data = $chart['data'];

			return view('reports.chart', compact('type', 'data', 'size'));
		} catch (\Exception $e) {
			return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
		}		
	}
}