<?php

namespace App\Services;

use Carbon\Carbon;

class ReportService
{

	private $results;

	private $date;

	public function __construct()
	{
		$this->date = Carbon::now('Asia/Jakarta');
	}

	public function createReport($request)
	{
		$query = \DB::table('snaps')
			->join('snap_files', 'snaps.id', '=', 'snap_files.snap_id')
            ->join('snap_tags', 'snap_files.id', '=', 'snap_tags.snap_file_id')
            ->join('members', 'members.id', '=', 'snaps.member_id')
            ->join('provinces', 'provinces.id', '=', 'members.province_id')
            ->select(
            	'members.id as user_id',
            	'members.dob as age',
            	'members.gender',
            	'members.occupation',
            	'members.person_in_house',
            	'members.last_education',
            	'members.city as users_city',
            	'members.monthly_expense_code as sec',
            	'members.marital_status as usership',
            	'provinces.name as province',
            	'snaps.receipt_id as receipt_number',
            	'snaps.outlet_type',
            	'snaps.outlet_name',
            	'snaps.outlet_province',
            	'snaps.outlet_city',
            	'snaps.location as outlet_address',
            	'snap_tags.name as products',
            	'snap_tags.brands as brand',
            	'snap_tags.quantity',
            	'snap_tags.total_price as total_price_quantity',
            	'snap_tags.total_price as grand_total_price',
            	'snaps.purchase_time as purchase_date',
            	'snaps.created_at as sent_time'
            );

		if ($request->has('date_create')) {
			$value = $request->input('date_create');
			$valueArray = explode(' - ', $value);
			$query->whereBetween('snaps.created_at',$valueArray);
		}
		if ($request->has('user_id')) {
			$value = $request->input('user_id');
			$valueArray = explode(',', $value);
			$query->whereIn('members.id',$valueArray);
		}
		if ($request->has('users_city')) {
			$value = $request->input('users_city');
			$valueArray = explode(',', $value);
			$query->whereIn('members.city',$valueArray);
		}
		if ($request->has('gender')) {
			$value = $request->input('gender');
			$valueArray = explode(',', $value);
			$query->whereIn('members.gender',$valueArray);
		}
		if ($request->has('province')) {
			$value = $request->input('province');
			$valueArray = explode(',', $value);
			$query->whereIn('provinces.name',$valueArray);
		}
		if ($request->has('age')) {
			$value = $request->input('age');
			$valueArray = explode(';', $value);

			$yearNow = $this->date->year;
			$youngYear = $yearNow - $valueArray[0];
			$oldYear = $yearNow - $valueArray[1];

			$query->whereYear('members.dob','<=', $youngYear);
			$query->whereYear('members.dob','>=', $oldYear);
		}
		if ($request->has('occupation')) {
			$value = $request->input('occupation');
			if (strtolower($value) != 'select occupation') {
				$query->where('members.occupation',$value);
			}
		}
		if ($request->has('person_in_house')) {
			$value = $request->input('person_in_house');
			$valueArray = explode(';', $value);
			$query->whereBetween('members.person_in_house',$valueArray);
		}
		if ($request->has('sec')) {
			$value = $request->input('sec');
			if (strtolower($value) != 'select sec') {
				$query->where('members.monthly_expense_code',$value);
			}
		}
		if ($request->has('last_education')) {
			$value = $request->input('last_education');
			$valueArray = explode(',', $value);
			$query->whereIn('members.last_education',$valueArray);
		}
		if ($request->has('receipt_number')) {
			$value = $request->input('receipt_number');
			if (strtolower($value) != '') {
				$query->where('snaps.receipt_id',$value);
			}
		}
		if ($request->has('outlet_type')) {
			$value = $request->input('outlet_type');
			if (strtolower($value) != 'select outlet type') {
				$query->where('snaps.outlet_type',$value);
			}
		}
		if ($request->has('outlet_name')) {
			$value = $request->input('outlet_name');
			if (strtolower($value) != 'select outlet name') {
				$query->where('snaps.outlet_name',$value);
			}
		}
		if ($request->has('outlet_province')) {
			$value = $request->input('outlet_province');
			if (strtolower($value) != 'select outlet province') {
				$query->where('snaps.outlet_province',$value);
			}
		}
		if ($request->has('outlet_city')) {
			$value = $request->input('outlet_city');
			if (strtolower($value) != 'select outlet city') {
				$query->where('snaps.outlet_city',$value);
			}
		}
		if ($request->has('outlet_address')) {
			$value = $request->input('outlet_address');
			if (strtolower($value) != '') {
				$query->where('snaps.location', 'like', '%'.$value.'%');
			}
		}
		if ($request->has('products')) {
			$value = $request->input('products');
			$valueArray = explode(',', $value);
			$query->whereIn('snap_tags.name',$valueArray);
		}
		if ($request->has('brand')) {
			$value = $request->input('brand');
			if (strtolower($value) != 'select brand') {
				$query->where('snap_tags.brands',$value);
			}
		}
		if ($request->has('quantity')) {
			$value = $request->input('quantity');
			$valueArray = explode(';', $value);
			$query->whereBetween('snap_tags.quantity',$valueArray);
		}
		if ($request->has('total_price_quantity')) {
			$value = $request->input('total_price_quantity');
			$valueArray = explode(';', $value);
			$query->whereBetween('snap_tags.total_price',$valueArray);
		}
		// if ($request->has('grand_total_price')) {
		// 	$value = $request->input('grand_total_price');
		// 	$valueArray = explode(';', $value);
		// 	$query->whereBetween('grand_total_price',$valueArray);
		// }
		// if ($request->has('purchase_date')) {
		// 	$value = $request->input('purchase_date');
		// 	$valueArray = explode(' - ', $value);
		// 	$query->whereBetween('snaps.purchase_time',$valueArray);
		// }
		// if ($request->has('sent_time')) {
		// 	$value = $request->input('sent_time');
		// 	$valueArray = explode(' - ', $value);
		// 	$query->whereBetween('snaps.created_at',$valueArray);
		// }

		$query->orderBy('snaps.id', 'DESC');

		$results = $query->paginate(50);
        $dataChart = $query->get();
        $this->setResultReport($dataChart);
		return $results;
	}

	public function getExportReportToCsv($data)
	{
		$query = \DB::table('snaps')
			->join('snap_files', 'snaps.id', '=', 'snap_files.snap_id')
            ->join('snap_tags', 'snap_files.id', '=', 'snap_tags.snap_file_id')
            ->join('members', 'members.id', '=', 'snaps.member_id')
            ->join('provinces', 'provinces.id', '=', 'members.province_id')
            ->select(
            	'members.id as user_id',
            	'members.dob as age',
            	'members.gender',
            	'members.occupation',
            	'members.person_in_house',
            	'members.last_education',
            	'members.city as users_city',
            	'members.monthly_expense_code as sec',
            	'members.marital_status as usership',
            	'provinces.name as province',
            	'snaps.receipt_id as receipt_number',
            	'snaps.outlet_type',
            	'snaps.outlet_name',
            	'snaps.outlet_province',
            	'snaps.outlet_city',
            	'snaps.location as outlet_address',
            	'snap_tags.name as products',
            	'snap_tags.brands as brand',
            	'snap_tags.quantity',
            	'snap_tags.total_price as total_price_quantity',
            	'snap_tags.total_price as grand_total_price',
            	'snaps.purchase_time as purchase_date',
            	'snaps.created_at as sent_time'
            );

		if (isset($data['date_create'])) {
			$value = $data['date_create'];
			$value = str_replace('%20', ' ', $value);
			$valueArray = explode(' - ', $value);
			$query->whereBetween('snaps.created_at',$valueArray);
		}
		if (isset($data['user_id'])) {
			$value = $data['user_id'];
			$valueArray = explode(',', $value);
			$query->whereIn('members.id',$valueArray);
		}
		if (isset($data['users_city'])) {
			$value = $data['users_city'];
			$valueArray = explode(',', $value);
			$query->whereIn('members.city',$valueArray);
		}
		if (isset($data['gender'])) {
			$value = $data['gender'];
			$valueArray = explode(',', $value);
			$query->whereIn('members.gender',$valueArray);
		}
		if (isset($data['province'])) {
			$value = $data['province'];
			$valueArray = explode(',', $value);
			$query->whereIn('provinces.name',$valueArray);
		}
		if (isset($data['age'])) {
			$value = $data['age'];
			$valueArray = explode(';', $value);

			$yearNow = $this->date->year;
			$youngYear = $yearNow - $valueArray[0];
			$oldYear = $yearNow - $valueArray[1];

			$query->whereYear('members.dob','<=', $youngYear);
			$query->whereYear('members.dob','>=', $oldYear);
		}
		if (isset($data['occupation'])) {
			$value = $data['occupation'];
			$value = str_replace('%20', ' ', $value);
			if (strtolower($value) != 'select occupation') {
				$query->where('members.occupation',$value);
			}
		}
		if (isset($data['person_in_house'])) {
			$value = $data['person_in_house'];
			$valueArray = explode(';', $value);
			$query->whereBetween('members.person_in_house',$valueArray);
		}
		if (isset($data['sec'])) {
			$value = $data['sec'];
			$value = str_replace('%20', ' ', $value);
			if (strtolower($value) != 'select sec') {
				$query->where('members.monthly_expense_code',$value);
			}
		}
		if (isset($data['last_education'])) {
			$value = $data['last_education'];
			$valueArray = explode(',', $value);
			$query->whereIn('members.last_education',$valueArray);
		}
		if (isset($data['receipt_number'])) {
			$value = $data['receipt_number'];
			if (strtolower($value) != '') {
				$query->where('snaps.receipt_id',$value);
			}
		}
		if (isset($data['outlet_type'])) {
			$value = $data['outlet_type'];
			$value = str_replace('%20', ' ', $value);
			if (strtolower($value) != 'select outlet type') {
				$query->where('snaps.outlet_type',$value);
			}
		}
		if (isset($data['outlet_name'])) {
			$value = $data['outlet_name'];
			$value = str_replace('%20', ' ', $value);
			if (strtolower($value) != 'select outlet name') {
				$query->where('snaps.outlet_name',$value);
			}
		}
		if (isset($data['outlet_province'])) {
			$value = $data['outlet_province'];
			$value = str_replace('%20', ' ', $value);
			if (strtolower($value) != 'select outlet province') {
				$query->where('snaps.outlet_province',$value);
			}
		}
		if (isset($data['outlet_city'])) {
			$value = $data['outlet_city'];
			$value = str_replace('%20', ' ', $value);
			if (strtolower($value) != 'select outlet city') {
				$query->where('snaps.outlet_city',$value);
			}
		}
		if (isset($data['outlet_address'])) {
			$value = $data['outlet_address'];
			$value = str_replace('%20', ' ', $value);
			if (strtolower($value) != '') {
				$query->where('snaps.location', 'like', '%'.$value.'%');
			}
		}
		if (isset($data['products'])) {
			$value = $data['products'];
			$valueArray = explode(',', $value);
			$query->whereIn('snap_tags.name',$valueArray);
		}
		if (isset($data['brand'])) {
			$value = $data['brand'];
			$value = str_replace('%20', ' ', $value);
			if (strtolower($value) != 'select brand') {
				$query->where('snap_tags.brands',$value);
			}
		}
		if (isset($data['quantity'])) {
			$value = $data['quantity'];
			$valueArray = explode(';', $value);
			$query->whereBetween('snap_tags.quantity',$valueArray);
		}
		if (isset($data['total_price_quantity'])) {
			$value = $data['total_price_quantity'];
			$valueArray = explode(';', $value);
			$query->whereBetween('snap_tags.total_price',$valueArray);
		}
		// if ($request->has('grand_total_price')) {
		// 	$value = $request->input('grand_total_price');
		// 	$valueArray = explode(';', $value);
		// 	$query->whereBetween('grand_total_price',$valueArray);
		// }
		// if ($request->has('purchase_date')) {
		// 	$value = $request->input('purchase_date');
		// 	$valueArray = explode(' - ', $value);
		// 	$query->whereBetween('snaps.purchase_time',$valueArray);
		// }
		// if ($request->has('sent_time')) {
		// 	$value = $request->input('sent_time');
		// 	$valueArray = explode(' - ', $value);
		// 	$query->whereBetween('snaps.created_at',$valueArray);
		// }

		$query->orderBy('snaps.id', 'DESC');

		$results = $query->paginate(50);

        if ($data['type'] == 'new') {
            $filename = strtolower(str_random(10)) . '.csv';
            $title = 'No,User Id,Age,Gender,Occupation,Person In House,Last Education,City,SEC,Receipt Number,Outlet Type,Outlet Name,Outlet Province,Outlet City,Outlet Address,Product,Brand,Quantity,Total Price,Grand Total Price,Purchase Date,Sent Date';
            \Storage::disk('csv')->put($filename, $title);
            $no = 1;
            foreach ($results as $row) {
                $baris = $no . ',' . $row->user_id . ',' . $row->age . ',' . $row->gender. ',' . $row->occupation
                . ',' . $row->person_in_house. ',' . $row->last_education. ',' . $row->users_city. ',' . $row->sec
                . ',' . $row->receipt_number. ',' . $row->outlet_type. ',' . $row->outlet_name. ',' . $row->outlet_province
                . ',' . $row->outlet_city. ',' . trim(str_replace(["\n", "\r", ","], ' ',$row->outlet_address))
                . ','. trim(str_replace(["\n", "\r", ","], ' ',$row->products)). ',' . trim(str_replace(["\n", "\r", ","], ' ',$row->brand))
                . ',' . trim(str_replace(["\n", "\r", ","], ' ',$row->quantity)). ',' . trim(str_replace(["\n", "\r", ","], ' ',$row->total_price_quantity))
                . ',' . $row->grand_total_price. ',' . $row->purchase_date. ',' . $row->sent_time;
                \Storage::disk('csv')->append($filename, $baris);
                $no++;
            }

        } else {
            if ($data['type'] == 'next') {
                $filename = $data['filename'];
                $no = $data['no'];
                foreach ($results as $row) {
                    $baris = $no . ',' . $row->user_id . ',' . $row->age . ',' . $row->gender. ',' . $row->occupation
	                . ',' . $row->person_in_house. ',' . $row->last_education. ',' . $row->users_city. ',' . $row->sec
	                . ',' . $row->receipt_number. ',' . $row->outlet_type. ',' . $row->outlet_name. ',' . $row->outlet_province
	                . ',' . $row->outlet_city. ',' . trim(str_replace(["\n", "\r", ","], ' ',$row->outlet_address))
	                . ','. trim(str_replace(["\n", "\r", ","], ' ',$row->products)). ',' . trim(str_replace(["\n", "\r", ","], ' ',$row->brand))
	                . ',' . trim(str_replace(["\n", "\r", ","], ' ',$row->quantity)). ',' . trim(str_replace(["\n", "\r", ","], ' ',$row->total_price_quantity))
	                . ',' . $row->grand_total_price. ',' . $row->purchase_date. ',' . $row->sent_time;
                	\Storage::disk('csv')->append($filename, $baris);
                    $no++;
                }
            }
        }

        $lastPage = $results->lastPage();
        $params = [
            'type_request' => ($lastPage == $data['page'] || count($results) == 0) ? 'download' : 'next',
            'filename' => $filename,
            'page' => $data['page'] + 1,
            'no' => $no,
            'last' => $lastPage,
        ];

        return $params;
	}

	public function createChart($request)
	{
		$datasetRequest = $request->input('dataset');
		$chartXRequest = $request->input('chart_x');
		//$chartYRequest = $request->input('chart_y');

  		$dataset = $request->input($datasetRequest);
  		$chartType = $request->input('chart_type');
  		$chartX = $request->input($chartXRequest);
  		//$chartY = $request->input($chartYRequest);

  		$datasetArray = $this->multiexplode(array(",",".","|",":",";"),$dataset);
  		$chartXArray = $this->multiexplode(array(",",".","|",":",";"),$chartX);
  		//$chartYArray = $this->multiexplode(array(",",".","|",":",";"),$chartY);

  		$data = [
  			'type' => $chartType,
  			'dataset' => $datasetArray,
  			'chartX' => $chartXArray,
  			//'chartY' => $chartYArray,
  		];

  		$request = [
  			'datasetRequest' => $datasetRequest,
  			'chartXRequest' => $chartXRequest,
  			//'chartYRequest' => $chartYRequest,
  		];

  		$charts = $this->buildChartGeneral($data, $request);

  		return $charts;
	}

	private function multiexplode($delimiters,$string) {
	    $ready = str_replace($delimiters, $delimiters[0], $string);
	    $launch = explode($delimiters[0], $ready);
	    return  $launch;
	}

	private function buildChartGeneral($data, $request)
	{

		$type = $data['type'];
		$dataset = $data['dataset'];
		$chartX = $data['chartX'];
		//$chartY = $data['chartY'];

		if(is_numeric($dataset[0])){
		 	$startDataset = $dataset[0];
		 	$countDataset = $dataset[1];
		 	$datasetNumeric = true;
		} else {
			$startDataset = 0;
		 	$countDataset = count($dataset);
		 	$datasetNumeric = false;
		}

		if(is_numeric($chartX[0])){
		 	$startchartX = $chartX[0];
		 	$countchartX = $chartX[1];
		 	$xNumeric = true;
		} else {
			$startchartX = 0;
		 	$countchartX = count($chartX);
		 	$xNumeric = false;
		}

		$labels = null;
		$content = null;
		if ($type == 'line' || $type == 'bar' || $type == 'radar' || $type == 'horizontalBar') {
			$content = [];
			$no = 0;
			if ($datasetNumeric) {
                for ($e=$startDataset; $e <= $countDataset; $e++) {
                    $search = $e;
                    $background = config('common.chart.color.'.$no)['background'];
                    $border = config('common.chart.color.'.$no)['border'];

                    $labels = [];
                    $data = [];
                    if ($xNumeric) {
                        for ($i=$startchartX; $i <= $countchartX; $i++) {
                            $searchX = $i;
                            $labels[] = $searchX;

                            $results = $this->getResultReport();

                            $results = $results->map(function($entry) {
                                $year = $this->date->year;
                                $ageArr = explode('-', $entry->age);
                                $age = $year - $ageArr[0];
                                return [
                                    "user_id" => $entry->user_id,
                                    "age" => $age,
                                    "gender" => $entry->gender,
                                    "occupation" => $entry->occupation,
                                    "person_in_house" => $entry->person_in_house,
                                    "last_education" => $entry->last_education,
                                    "users_city" => $entry->users_city,
                                    "sec" => $entry->sec,
                                    "usership" => $entry->usership,
                                    "province" => $entry->province,
                                    "receipt_number" => $entry->receipt_number,
                                    "outlet_type" => $entry->outlet_type,
                                    "outlet_name" => $entry->outlet_name,
                                    "outlet_province" => $entry->outlet_province,
                                    "outlet_city" => $entry->outlet_city,
                                    "outlet_address" => $entry->outlet_address,
                                    "products" => $entry->products,
                                    "brand" => $entry->brand,
                                    "quantity" => $entry->quantity,
                                    "total_price_quantity" => $entry->total_price_quantity,
                                    "grand_total_price" => $entry->grand_total_price,
                                    "purchase_date" => $entry->purchase_date,
                                    "sent_time" => $entry->sent_time,
                                ];
                            });

                            $results = $results->filter(function($value, $Key) use ($request, $search, $searchX) {
                                return $value[$request['datasetRequest']] == $search &&
                                    $value[$request['chartXRequest']] == $searchX;
                            });

                            $data[] = count($results);
                        }
                    } else {
                        for ($i=$startchartX; $i < $countchartX; $i++) {
                            $searchX = $chartX[$i];
                            $labels[] = $searchX;

                            $results = $this->getResultReport();

                            $results = $results->map(function($entry) {
                                $year = $this->date->year;
                                $ageArr = explode('-', $entry->age);
                                $age = $year - $ageArr[0];
                                return [
                                    "user_id" => $entry->user_id,
                                    "age" => $age,
                                    "gender" => $entry->gender,
                                    "occupation" => $entry->occupation,
                                    "person_in_house" => $entry->person_in_house,
                                    "last_education" => $entry->last_education,
                                    "users_city" => $entry->users_city,
                                    "sec" => $entry->sec,
                                    "usership" => $entry->usership,
                                    "province" => $entry->province,
                                    "receipt_number" => $entry->receipt_number,
                                    "outlet_type" => $entry->outlet_type,
                                    "outlet_name" => $entry->outlet_name,
                                    "outlet_province" => $entry->outlet_province,
                                    "outlet_city" => $entry->outlet_city,
                                    "outlet_address" => $entry->outlet_address,
                                    "products" => $entry->products,
                                    "brand" => $entry->brand,
                                    "quantity" => $entry->quantity,
                                    "total_price_quantity" => $entry->total_price_quantity,
                                    "grand_total_price" => $entry->grand_total_price,
                                    "purchase_date" => $entry->purchase_date,
                                    "sent_time" => $entry->sent_time,
                                ];
                            });

                            $results = $results->filter(function($value, $Key) use ($request, $search, $searchX) {
                                return $value[$request['datasetRequest']] == $search &&
                                    $value[$request['chartXRequest']] == $searchX;
                            });

                            $data[] = count($results);
                        }
                    }

                    $content[] = [
                        "label" => $search,
                        "backgroundColor" => $background,
                        "borderColor" => $border,
                        "data" => $data,
                        "borderWidth" => 2,
                        "fill" => false,
                        "lineTension" => 0.1,
                        "spanGaps" => false,
                        "borderCapStyle" => 'butt',
                        "borderDash" => [],
                        "borderDashOffset" => 0.0,
                        "borderJoinStyle" => 'miter',
                        "pointBackgroundColor" => "#fff",
                        "pointBorderWidth" => 1,
                        "pointHoverRadius" => 5,
                        "pointHoverBackgroundColor" => $background,
                        "pointHoverBorderColor" => $border,
                        "pointHoverBorderWidth" => 2,
                        "pointRadius" => 1,
                        "pointHitRadius" => 10,
                    ];

                    $no++;
                }
            } else {
                for ($e=$startDataset; $e < $countDataset; $e++) {
                    $search = $dataset[$e];
                    $background = config('common.chart.color.'.$no)['background'];
                    $border = config('common.chart.color.'.$no)['border'];

                    $labels = [];
                    $data = [];
                    if ($xNumeric) {
                        for ($i=$startchartX; $i <= $countchartX; $i++) {
                            $searchX = $i;
                            $labels[] = $searchX;

                            $results = $this->getResultReport();

                            $results = $results->map(function($entry) {
                                $year = $this->date->year;
                                $ageArr = explode('-', $entry->age);
                                $age = $year - $ageArr[0];
                                return [
                                    "user_id" => $entry->user_id,
                                    "age" => $age,
                                    "gender" => $entry->gender,
                                    "occupation" => $entry->occupation,
                                    "person_in_house" => $entry->person_in_house,
                                    "last_education" => $entry->last_education,
                                    "users_city" => $entry->users_city,
                                    "sec" => $entry->sec,
                                    "usership" => $entry->usership,
                                    "province" => $entry->province,
                                    "receipt_number" => $entry->receipt_number,
                                    "outlet_type" => $entry->outlet_type,
                                    "outlet_name" => $entry->outlet_name,
                                    "outlet_province" => $entry->outlet_province,
                                    "outlet_city" => $entry->outlet_city,
                                    "outlet_address" => $entry->outlet_address,
                                    "products" => $entry->products,
                                    "brand" => $entry->brand,
                                    "quantity" => $entry->quantity,
                                    "total_price_quantity" => $entry->total_price_quantity,
                                    "grand_total_price" => $entry->grand_total_price,
                                    "purchase_date" => $entry->purchase_date,
                                    "sent_time" => $entry->sent_time,
                                ];
                            });

                            $results = $results->filter(function($value, $Key) use ($request, $search, $searchX) {
                                return $value[$request['datasetRequest']] == $search &&
                                    $value[$request['chartXRequest']] == $searchX;
                            });

                            $data[] = count($results);
                        }
                    } else {
                        for ($i=$startchartX; $i < $countchartX; $i++) {
                            $searchX = $chartX[$i];
                            $labels[] = $searchX;

                            $results = $this->getResultReport();

                            $results = $results->map(function($entry) {
                                $year = $this->date->year;
                                $ageArr = explode('-', $entry->age);
                                $age = $year - $ageArr[0];
                                return [
                                    "user_id" => $entry->user_id,
                                    "age" => $age,
                                    "gender" => $entry->gender,
                                    "occupation" => $entry->occupation,
                                    "person_in_house" => $entry->person_in_house,
                                    "last_education" => $entry->last_education,
                                    "users_city" => $entry->users_city,
                                    "sec" => $entry->sec,
                                    "usership" => $entry->usership,
                                    "province" => $entry->province,
                                    "receipt_number" => $entry->receipt_number,
                                    "outlet_type" => $entry->outlet_type,
                                    "outlet_name" => $entry->outlet_name,
                                    "outlet_province" => $entry->outlet_province,
                                    "outlet_city" => $entry->outlet_city,
                                    "outlet_address" => $entry->outlet_address,
                                    "products" => $entry->products,
                                    "brand" => $entry->brand,
                                    "quantity" => $entry->quantity,
                                    "total_price_quantity" => $entry->total_price_quantity,
                                    "grand_total_price" => $entry->grand_total_price,
                                    "purchase_date" => $entry->purchase_date,
                                    "sent_time" => $entry->sent_time,
                                ];
                            });

                            $results = $results->filter(function($value, $Key) use ($request, $search, $searchX) {
                                return $value[$request['datasetRequest']] == $search &&
                                    $value[$request['chartXRequest']] == $searchX;
                            });

                            $data[] = count($results);
                        }
                    }

                    $content[] = [
                        "label" => $search,
                        "backgroundColor" => $background,
                        "borderColor" => $border,
                        "data" => $data,
                        "borderWidth" => 2,
                        "fill" => false,
                        "lineTension" => 0.1,
                        "spanGaps" => false,
                        "borderCapStyle" => 'butt',
                        "borderDash" => [],
                        "borderDashOffset" => 0.0,
                        "borderJoinStyle" => 'miter',
                        "pointBackgroundColor" => "#fff",
                        "pointBorderWidth" => 1,
                        "pointHoverRadius" => 5,
                        "pointHoverBackgroundColor" => $background,
                        "pointHoverBorderColor" => $border,
                        "pointHoverBorderWidth" => 2,
                        "pointRadius" => 1,
                        "pointHitRadius" => 10,
                    ];

                    $no++;
                }
            }

		} else if ($type == 'pie' || $type == 'doughnut' || $type == 'polarArea') {
			$totalData = [];
			$labels = [];
			$background = [];
			$border = [];
			$no = 0;

            if ($datasetNumeric) {
                for ($e=$startDataset; $e <= $countDataset; $e++) {
                    $search = $e;
                    $background[] = config('common.chart.color.'.$no)['background'];
                    $border[] = config('common.chart.color.'.$no)['border'];
                    $labels[] = $search;
                    $data = [];
                    if ($xNumeric) {
                        for ($i=$startchartX; $i <= $countchartX; $i++) {
                            $searchX = $i;

                            $results = $this->getResultReport();

                            $results = $results->map(function($entry) {
                                $year = $this->date->year;
                                $ageArr = explode('-', $entry->age);
                                $age = $year - $ageArr[0];
                                return [
                                    "user_id" => $entry->user_id,
                                    "age" => $age,
                                    "gender" => $entry->gender,
                                    "occupation" => $entry->occupation,
                                    "person_in_house" => $entry->person_in_house,
                                    "last_education" => $entry->last_education,
                                    "users_city" => $entry->users_city,
                                    "sec" => $entry->sec,
                                    "usership" => $entry->usership,
                                    "province" => $entry->province,
                                    "receipt_number" => $entry->receipt_number,
                                    "outlet_type" => $entry->outlet_type,
                                    "outlet_name" => $entry->outlet_name,
                                    "outlet_province" => $entry->outlet_province,
                                    "outlet_city" => $entry->outlet_city,
                                    "outlet_address" => $entry->outlet_address,
                                    "products" => $entry->products,
                                    "brand" => $entry->brand,
                                    "quantity" => $entry->quantity,
                                    "total_price_quantity" => $entry->total_price_quantity,
                                    "grand_total_price" => $entry->grand_total_price,
                                    "purchase_date" => $entry->purchase_date,
                                    "sent_time" => $entry->sent_time,
                                ];
                            });

                            $results = $results->filter(function($value, $Key) use ($request, $search, $searchX) {
                                return $value[$request['datasetRequest']] == $search &&
                                    $value[$request['chartXRequest']] == $searchX;
                            });

                            $data[] = count($results);
                        }
                    } else {
                        for ($i=$startchartX; $i < $countchartX; $i++) {
                            $searchX = $chartX[$i];

                            $results = $this->getResultReport();

                            $results = $results->map(function($entry) {
                                $year = $this->date->year;
                                $ageArr = explode('-', $entry->age);
                                $age = $year - $ageArr[0];
                                return [
                                    "user_id" => $entry->user_id,
                                    "age" => $age,
                                    "gender" => $entry->gender,
                                    "occupation" => $entry->occupation,
                                    "person_in_house" => $entry->person_in_house,
                                    "last_education" => $entry->last_education,
                                    "users_city" => $entry->users_city,
                                    "sec" => $entry->sec,
                                    "usership" => $entry->usership,
                                    "province" => $entry->province,
                                    "receipt_number" => $entry->receipt_number,
                                    "outlet_type" => $entry->outlet_type,
                                    "outlet_name" => $entry->outlet_name,
                                    "outlet_province" => $entry->outlet_province,
                                    "outlet_city" => $entry->outlet_city,
                                    "outlet_address" => $entry->outlet_address,
                                    "products" => $entry->products,
                                    "brand" => $entry->brand,
                                    "quantity" => $entry->quantity,
                                    "total_price_quantity" => $entry->total_price_quantity,
                                    "grand_total_price" => $entry->grand_total_price,
                                    "purchase_date" => $entry->purchase_date,
                                    "sent_time" => $entry->sent_time,
                                ];
                            });

                            $results = $results->filter(function($value, $Key) use ($request, $search, $searchX) {
                                return $value[$request['datasetRequest']] == $search &&
                                    $value[$request['chartXRequest']] == $searchX;
                            });

                            $data[] = count($results);
                        }
                    }

                    $totalData[] = collect($data)->sum();

                    $no++;
                }
            } else {
                for ($e=$startDataset; $e < $countDataset; $e++) {
                    $search = $dataset[$e];
                    $background[] = config('common.chart.color.'.$no)['background'];
                    $border[] = config('common.chart.color.'.$no)['border'];
                    $labels[] = $search;
                    $data = [];
                    if ($xNumeric) {
                        for ($i=$startchartX; $i <= $countchartX; $i++) {
                            $searchX = $i;

                            $results = $this->getResultReport();

                            $results = $results->map(function($entry) {
                                $year = $this->date->year;
                                $ageArr = explode('-', $entry->age);
                                $age = $year - $ageArr[0];
                                return [
                                    "user_id" => $entry->user_id,
                                    "age" => $age,
                                    "gender" => $entry->gender,
                                    "occupation" => $entry->occupation,
                                    "person_in_house" => $entry->person_in_house,
                                    "last_education" => $entry->last_education,
                                    "users_city" => $entry->users_city,
                                    "sec" => $entry->sec,
                                    "usership" => $entry->usership,
                                    "province" => $entry->province,
                                    "receipt_number" => $entry->receipt_number,
                                    "outlet_type" => $entry->outlet_type,
                                    "outlet_name" => $entry->outlet_name,
                                    "outlet_province" => $entry->outlet_province,
                                    "outlet_city" => $entry->outlet_city,
                                    "outlet_address" => $entry->outlet_address,
                                    "products" => $entry->products,
                                    "brand" => $entry->brand,
                                    "quantity" => $entry->quantity,
                                    "total_price_quantity" => $entry->total_price_quantity,
                                    "grand_total_price" => $entry->grand_total_price,
                                    "purchase_date" => $entry->purchase_date,
                                    "sent_time" => $entry->sent_time,
                                ];
                            });

                            $results = $results->filter(function($value, $Key) use ($request, $search, $searchX) {
                                return $value[$request['datasetRequest']] == $search &&
                                    $value[$request['chartXRequest']] == $searchX;
                            });

                            $data[] = count($results);
                        }
                    } else {
                        for ($i=$startchartX; $i < $countchartX; $i++) {
                            $searchX = $chartX[$i];

                            $results = $this->getResultReport();

                            $results = $results->map(function($entry) {
                                $year = $this->date->year;
                                $ageArr = explode('-', $entry->age);
                                $age = $year - $ageArr[0];
                                return [
                                    "user_id" => $entry->user_id,
                                    "age" => $age,
                                    "gender" => $entry->gender,
                                    "occupation" => $entry->occupation,
                                    "person_in_house" => $entry->person_in_house,
                                    "last_education" => $entry->last_education,
                                    "users_city" => $entry->users_city,
                                    "sec" => $entry->sec,
                                    "usership" => $entry->usership,
                                    "province" => $entry->province,
                                    "receipt_number" => $entry->receipt_number,
                                    "outlet_type" => $entry->outlet_type,
                                    "outlet_name" => $entry->outlet_name,
                                    "outlet_province" => $entry->outlet_province,
                                    "outlet_city" => $entry->outlet_city,
                                    "outlet_address" => $entry->outlet_address,
                                    "products" => $entry->products,
                                    "brand" => $entry->brand,
                                    "quantity" => $entry->quantity,
                                    "total_price_quantity" => $entry->total_price_quantity,
                                    "grand_total_price" => $entry->grand_total_price,
                                    "purchase_date" => $entry->purchase_date,
                                    "sent_time" => $entry->sent_time,
                                ];
                            });

                            $results = $results->filter(function($value, $Key) use ($request, $search, $searchX) {
                                return $value[$request['datasetRequest']] == $search &&
                                    $value[$request['chartXRequest']] == $searchX;
                            });

                            $data[] = count($results);
                        }
                    }

                    $totalData[] = collect($data)->sum();

                    $no++;
                }
            }

			$content = [
		    	[
	                'backgroundColor' => $border,
	                'hoverBackgroundColor' => $background,
	                'data' => $totalData
            	]
            ];

		}

		$chartjs = [
            "labels" => $labels,
            "datasets" => $content
        ];

		return $chartjs;
	}

	private function setResultReport($value)
	{
		$this->results = $value;
		return $this;
	}

	private function getResultReport()
	{
		return $this->results;
	}

}