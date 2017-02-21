<?php

namespace App\Services;

class ReportService
{

	public $setSize;

	public function buildChart($request)
	{
		$type = $request->input('type');
		$dataset = $request->input('dataset');
		$monthLabels = $request->input('month_labels');
		$countDataset = count($dataset);
		$year = \Carbon\Carbon::today()->year;
		$monthArray = explode(';', $monthLabels);

		if ($type == 'line' || $type == 'bar' || $type == 'radar' || $type == 'horizontalBar') {
			$content = [];
			for ($e=0; $e < $countDataset; $e++) { 
				$search = $dataset[$e];
				$background = config('common.chart.color.'.$e)['background'];
				$border = config('common.chart.color.'.$e)['border'];

				$labels = [];
				$data = [];
				for ($i=$monthArray[0]; $i <= $monthArray[1]; $i++) { 
					$labels[] = config('common.chart.month.'.$i);
					$reports = \DB::table('reports')
					->where('products', 'like', $search.'%')
					->whereMonth('created_at', $i)
					->whereYear('created_at', $year)
					->select('products')
					->get();

					$data[] = count($reports);
				}

				$content[] = [
			    	"label" => $search,
	                "backgroundColor" => $background,
	                "borderColor" => $border,
	                "data" => $data,
	                "borderWidth" => 1,
	            ];
			}

			$size = [
				'padding' => 20,
				'width' => 80,
			];

			$this->setSize($size);

		} else if ($type == 'pie' || $type == 'doughnut' || $type == 'polarArea') {
			$totalData = [];
			$labels = [];
			$background = [];
			$border = [];
			for ($e=0; $e < $countDataset; $e++) { 
				$search = $dataset[$e];
				$background[] = config('common.chart.color.'.$e)['background'];
				$border[] = config('common.chart.color.'.$e)['border'];
				$labels[] = $search;
				$data = [];
				for ($i=$monthArray[0]; $i <= $monthArray[1]; $i++) { 					
					$reports = \DB::table('reports')
					->where('products', 'like', $search.'%')
					->whereMonth('created_at', $i)
					->whereYear('created_at', $year)
					->select('products')
					->get();

					$data[] = count($reports);
				}

				$totalData[] = collect($data)->sum();				
			}

			$content = [
		    	[
	                'backgroundColor' => $border,
	                'hoverBackgroundColor' => $background,
	                'data' => $totalData
            	]
            ];

			$size = [
				'padding' => 30,
				'width' => 50,
			];

			$this->setSize($size);
		}			

		$chartjs = [
            "type" => $type,
            "data" => [
                "labels" => $labels,
                "datasets" => $content
            ],
        ];

		return $chartjs;
	}

	public function setSize($value)
	{
		$this->setSize = $value;
		return $this;
	}

	public function getSize()
	{
		return $this->setSize;
	}
}