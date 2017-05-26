<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use App\Services\ReportService;
use App\Reports;
use Illuminate\Pagination\LengthAwarePaginator;

class ReportController extends AdminController
{
	public function index(Request $request)
	{	

		if ($request->has('create_report')) {
			$requestAll = $request->all();
			$reportService = (new ReportService);
			$results = $reportService->createReport($request);
			//$charts = $reportService->createChart($request, $results);
			$charts = [];
			$chartType = $request->input('chart_type');
			foreach ($requestAll as $key => $value) {
				$results->appends($key, $value);
			}
		} else {
			$results = [];
			$charts = [];
			$chartType = '';
		}		
		$member = \DB::table('members')
			->join('provinces', 'provinces.id', '=', 'members.province_id')
			->distinct();
		$snap = \DB::table('snaps')->distinct();
		$snapTag = \DB::table('snap_tags')->distinct();

		if (cache('members')) {
			$members = cache('members');
		} else {
			$members = $member->get(['members.id']);
			cache()->put('members', $members, 1440);
		}

		if (cache('gender')) {
			$gender = cache('gender');
		} else {
			$gender = $member->get(['members.gender']);
			cache()->put('gender', $gender, 1440);
		}

		if (cache('oc')) {
			$oc = cache('oc');
		} else {
			$oc = $member->get(['members.occupation']);
			cache()->put('oc', $oc, 1440);
		}

		if (cache('le')) {
			$le = cache('le');
		} else {
			$le = $member->get(['members.last_education']);
			cache()->put('le', $le, 1440);
		}

		if (cache('citys')) {
			$citys = cache('citys');
		} else {
			$citys = $member->get(['members.city']);
			cache()->put('citys', $citys, 1440);
		}

		if (cache('provinces')) {
			$provinces = cache('provinces');
		} else {
			$provinces = $member->get(['provinces.name']);
			cache()->put('provinces', $provinces, 1440);
		}

		if (cache('outletType')) {
			$outletType = cache('outletType');
		} else {
			$outletType = $snap->get(['outlet_type']);
			cache()->put('outletType', $outletType, 1440);
		}

		if (cache('outletName')) {
			$outletName = cache('outletName');
		} else {
			$outletName = $snap->get(['outlet_name']);
			cache()->put('outletName', $outletName, 1440);
		}

		if (cache('outletProvince')) {
			$outletProvince = cache('outletProvince');
		} else {
			$outletProvince = $snap->get(['outlet_province']);
			cache()->put('outletProvince', $outletProvince, 1440);
		}

		if (cache('outletCity')) {
			$outletCity = cache('outletCity');
		} else {
			$outletCity = $snap->get(['outlet_city']);
			cache()->put('outletCity', $outletCity, 1440);
		}

		if (cache('products')) {
			$products = cache('products');
		} else {
			$products = $snapTag->get(['name']);
			cache()->put('products', $products, 1440);
		}

		if (cache('brands')) {
			$brands = cache('brands');
		} else {
			$brands = $snapTag->get(['brands']);
			cache()->put('brands', $brands, 1440);
		}

		if (cache('sec')) {
			$sec = cache('sec');
		} else {
			$sec = $member->get(['monthly_expense_code']);
			cache()->put('sec', $sec, 1440);
		}

		if (cache('age')) {
			$age = cache('age');
		} else {
			$age = \DB::select('SELECT (SELECT DISTINCT YEAR(CURRENT_TIMESTAMP) - YEAR(dob) - (RIGHT(CURRENT_TIMESTAMP, 5) < RIGHT(dob, 5)) AS age 
  				FROM members WHERE dob IS NOT NULL ORDER BY age LIMIT 1) as "first",(SELECT DISTINCT YEAR(CURRENT_TIMESTAMP) - YEAR(dob) - (RIGHT(CURRENT_TIMESTAMP, 5) < RIGHT(dob, 5)) AS age 
  				FROM members ORDER BY age DESC LIMIT 1) as "last"')[0];
			cache()->put('age', $age, 1440);
		}

		if (cache('pih')) {
			$pih = cache('pih');
		} else {
			$pih = \DB::select('SELECT (SELECT DISTINCT person_in_house FROM members ORDER BY person_in_house LIMIT 1) as "first",(SELECT DISTINCT person_in_house FROM members ORDER BY person_in_house DESC LIMIT 1) as "last"')[0];
			cache()->put('pih', $pih, 1440);
		}

		if (cache('qty')) {
			$qty = cache('qty');
		} else {
			$qty = \DB::select('SELECT (SELECT DISTINCT quantity FROM snap_tags ORDER BY quantity LIMIT 1) as "first",(SELECT DISTINCT quantity FROM snap_tags ORDER BY quantity DESC LIMIT 1) as "last"')[0];
			cache()->put('qty', $qty, 1440);
		}

		if (cache('total')) {
			$total = cache('total');
		} else {
			$total = \DB::select('SELECT (SELECT DISTINCT total_price FROM snap_tags ORDER BY total_price LIMIT 1) as "first",(SELECT DISTINCT total_price FROM snap_tags ORDER BY total_price DESC LIMIT 1) as "last"')[0];
			cache()->put('total', $total, 1440);
		}

		if (cache('grandTotal')) {
			$grandTotal = cache('grandTotal');
		} else {
			$grandTotal = \DB::select('SELECT (SELECT DISTINCT quantity * total_price as grand_total_price FROM snap_tags ORDER BY grand_total_price LIMIT 1) as "first",(SELECT DISTINCT quantity * total_price as grand_total_price FROM snap_tags ORDER BY grand_total_price DESC LIMIT 1) as "last"')[0];
			cache()->put('grandTotal', $grandTotal, 1440);
		}

		$configs = null;
		if ($request->has('config')) {
			$configs = $request->input('config');
			$configs = array_keys($configs);
		}		

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
			'configs',
			'charts',
			'chartType',
		];
		return view('reports.index', compact($data));
	}
}