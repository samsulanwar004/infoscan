<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use App\Services\ReportService;
use App\Reports;

class ReportController extends AdminController
{
	public function index(Request $request)
	{	
		$results = [];
		$query = \DB::table('reports');

		if ($request->has('date_create')) {
			$value = $request->input('date_create');
			$valueArray = explode(' - ', $value);
			$query->whereBetween('created_at',$valueArray);
		}
		if ($request->has('userid')) {
			$value = $request->input('userid');
			$valueArray = explode(',', $value);
			$query->whereIn('user_id',$valueArray);
		}
		if ($request->has('userscity')) {
			$value = $request->input('userscity');
			$valueArray = explode(',', $value);
			$query->whereIn('users_city',$valueArray);
		}	
		if ($request->has('genders')) {
			$value = $request->input('genders');
			$valueArray = explode(',', $value);
			$query->whereIn('gender',$valueArray);
		}		
		if ($request->has('provinces')) {
			$value = $request->input('provinces');
			$valueArray = explode(',', $value);
			$query->whereIn('province',$valueArray);
		}
		if ($request->has('age')) {			
			$value = $request->input('age');
			$valueArray = explode(';', $value);
			$query->whereBetween('age',$valueArray);
		}
		if ($request->has('occupation')) {
			$value = $request->input('occupation');
			if (strtolower($value) != 'select occupation') {
				$query->where('occupation',$value);
			}				
		}
		if ($request->has('person_in_house')) {
			$value = $request->input('person_in_house');
			$valueArray = explode(';', $value);
			$query->whereBetween('person_in_house',$valueArray);
		}
		if ($request->has('sec')) {
			$value = $request->input('sec');			
			if (strtolower($value) != 'select sec') {
				$query->where('sec',$value);
			}	
		}
		// if ($request->has('usership')) {
		// 	$value = $request->input('usership');
		// 	if (strtolower($value) != 'select usership') {
		// 		$query->where('usership',$value);
		// 	}
		// }
		if ($request->has('receipt_number')) {
			$value = $request->input('receipt_number');
			if (strtolower($value) != '') {
				$query->where('receipt_number',$value);
			}
		}
		if ($request->has('outlet_type')) {
			$value = $request->input('outlet_type');
			if (strtolower($value) != 'select outlet type') {
				$query->where('outlet_type',$value);
			}
		}
		if ($request->has('outlet_name')) {
			$value = $request->input('outlet_name');
			if (strtolower($value) != 'select outlet name') {
				$query->where('outlet_name',$value);
			}
		}
		if ($request->has('outlet_province')) {
			$value = $request->input('outlet_province');
			if (strtolower($value) != 'select outlet province') {
				$query->where('outlet_province',$value);
			}
		}
		if ($request->has('outlet_city')) {			
			$value = $request->input('outlet_city');
			if (strtolower($value) != 'select outlet city') {
				$query->where('outlet_city',$value);
			}
		}
		if ($request->has('outlet_address')) {
			$value = $request->input('outlet_address');
			if (strtolower($value) != '') {
				$query->where('outlet_address',$value);
			}
		}
		if ($request->has('lasteducation')) {
			$value = $request->input('lasteducation');
			$valueArray = explode(',', $value);
			$query->whereIn('last_education',$valueArray);
		}
		if ($request->has('product')) {
			$value = $request->input('product');
			$valueArray = explode(',', $value);
			$query->whereIn('products',$valueArray);
		}
		if ($request->has('brand')) {
			$value = $request->input('brand');
			if (strtolower($value) != 'select brand') {
				$query->where('brand',$value);
			}
		}
		if ($request->has('quantity')) {
			$value = $request->input('quantity');
			$valueArray = explode(';', $value);
			$query->whereBetween('quantity',$valueArray);
		}
		if ($request->has('total_price_quantity')) {
			$value = $request->input('total_price_quantity');
			$valueArray = explode(';', $value);
			$query->whereBetween('total_price_quantity',$valueArray);
		}
		if ($request->has('grand_total_price')) {
			$value = $request->input('grand_total_price');
			$valueArray = explode(';', $value);
			$query->whereBetween('grand_total_price',$valueArray);
		}
		if ($request->has('purchase_date')) {
			$value = $request->input('purchase_date');
			$valueArray = explode(' - ', $value);
			$query->whereBetween('purchase_date',$valueArray);
		}
		if ($request->has('sent_time')) {
			$value = $request->input('sent_time');
			$valueArray = explode(' - ', $value);
			$query->whereBetween('sent_time',$valueArray);
		}

		$results = $query->get();
		$distinct = \DB::table('reports')->distinct();
		$members = $distinct->get(['user_id']);
		$gender = $distinct->get(['gender']);
		$oc = $distinct->get(['occupation']);
		$le = $distinct->get(['last_education']);
		$citys = $distinct->get(['users_city']);
		$provinces = $distinct->get(['province']);
		$outletType = $distinct->get(['outlet_type']);
		$outletName = $distinct->get(['outlet_name']);
		$outletProvince = $distinct->get(['outlet_province']);
		$outletCity = $distinct->get(['outlet_city']);
		$products = $distinct->get(['products']);
		$brands = $distinct->get(['brand']);
		$sec = $distinct->get(['sec']);

		$age = \DB::select('SELECT (SELECT DISTINCT age FROM reports ORDER BY age LIMIT 1) as "first",(SELECT DISTINCT age FROM reports ORDER BY age DESC LIMIT 1) as "last"')[0];
		$pih = \DB::select('SELECT (SELECT DISTINCT person_in_house FROM reports ORDER BY person_in_house LIMIT 1) as "first",(SELECT DISTINCT person_in_house FROM reports ORDER BY person_in_house DESC LIMIT 1) as "last"')[0];
		$qty = \DB::select('SELECT (SELECT DISTINCT quantity FROM reports ORDER BY quantity LIMIT 1) as "first",(SELECT DISTINCT quantity FROM reports ORDER BY quantity DESC LIMIT 1) as "last"')[0];
		$total = \DB::select('SELECT (SELECT DISTINCT total_price_quantity FROM reports ORDER BY total_price_quantity LIMIT 1) as "first",(SELECT DISTINCT total_price_quantity FROM reports ORDER BY total_price_quantity DESC LIMIT 1) as "last"')[0];
		$grandTotal = \DB::select('SELECT (SELECT DISTINCT grand_total_price FROM reports ORDER BY grand_total_price LIMIT 1) as "first",(SELECT DISTINCT grand_total_price FROM reports ORDER BY grand_total_price DESC LIMIT 1) as "last"')[0];

		$data = [
			'results',
			'members',
			'gender',
			'oc',
			'le',
			'citys',
			'provinces',
			'outletType',
			'outletName',
			'outletProvince',
			'outletCity',
			'outletAddress',
			'products',
			'brands',
			'sec',
			'receipts',
			'age',
			'pih',
			'qty',
			'total',
			'grandTotal',
		];
		return view('reports.index', compact($data));
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