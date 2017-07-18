<?php
namespace App\Services\Chart;

use Carbon\Carbon;
use Excel as BaseExcel;

/**
 * Exporting chart data to excel
 */
class Excel
{

    protected $title;

    public function export($title, $timeRange, $data)
    {
        $this->title = $title;
        $methodName = "toXls" . ucfirst($timeRange);
        if (method_exists($this, $methodName)) {
            return $this->{$methodName}($data);
        }
    }

    protected function toXlsDaily(array $data)
    {

        BaseExcel::create('Statistics data', function ($excel) use ($data) {

            $excel->sheet('Sheetname', function ($sheet) use ($data) {
                $startDate = Carbon::now()->startOfWeek();

                $sheet->cell('A1', function ($cell) {
                    $cell->setValue($this->title . ' Daily');
                });

                $sheet->cell('A2', function ($cell) use ($startDate) {
                    $cell->setValue('of ' . $startDate->format('d-m-Y') . ' - ' . $startDate->addDays(6)->format('d-m-Y'));
                });

                // set column header as date format
                $sheet->setColumnFormat([
                    'B4:H4' => 'dd-mm-yyyy',
                ]);

                $columnsTitle = ['']; // set empty string as first array item
                $startDate    = Carbon::now()->startOfWeek();
                for ($i = 0; $i < 7; $i++) {
                    $columnsTitle[] = $startDate->format('d-m-Y');
                    $startDate->addDay();
                }

                $rowNum = 4;

                $sheet->row($rowNum, $columnsTitle);
                // dd($data);
                foreach ($data as $key => $item) {
                    $rowNum++;
                    $rowData = [title_case($key)];

                    for ($i = 0; $i < 7; $i++) {
                        if (isset($item[$i])) {
                            $rowData[] = $item[$i];
                        } else {
                            $rowData[] = 0;
                        }
                    }

                    $sheet->row($rowNum, $rowData);

                }

            });

        })->export('xls');

    }

    protected function toXlsWeekly(array $data)
    {

        BaseExcel::create('Statistics data', function ($excel) use ($data) {

            $excel->sheet('Sheetname', function ($sheet) use ($data) {
                $startDate = Carbon::now()->startOfWeek();

                $sheet->cell('A1', function ($cell) {
                    $cell->setValue($this->title . ' Weekly');
                });

                $sheet->cell('A2', function ($cell) use ($startDate) {
                    $cell->setValue('of ' . $startDate->format('F Y'));
                });

                $columnsTitle = [
                    'Week 1',
                    "Week 2",
                    "Week 3",
                    "Week 4",
                    "Week 5"
                ];

                $rowNum = 4;

                $sheet->row($rowNum, array_merge([""], $columnsTitle));

                foreach ($data as $key => $item) {
                    $rowNum++;
                    $rowData = [title_case($key)];

                    foreach($columnsTitle as $key => $title) {
                        if (isset($item[$key + 1])) {
                            $rowData[] = $item[$key + 1];
                        } else {
                            $rowData[] = 0;
                        }
                    }

                    $sheet->row($rowNum, $rowData);

                }

            });

        })->export('xls');
    }

    protected function toXlsMonthly(array $data)
    {

        BaseExcel::create('Statistics data', function ($excel) use ($data) {

            $excel->sheet('Sheetname', function ($sheet) use ($data) {
                $startDate = Carbon::now()->startOfWeek();

                $sheet->cell('A1', function ($cell) {
                    $cell->setValue($this->title . ' Monthly');
                });

                $sheet->cell('A2', function ($cell) use ($startDate) {
                    $cell->setValue('of ' . $startDate->format('Y'));
                });

                $columnsTitle = [
                    'January',
                    'February',
                    'March',
                    'April',
                    'May',
                    'June',
                    'July',
                    'August',
                    'September',
                    'October',
                    'November',
                    'December'
                ];

                $rowNum = 4;

                $sheet->row($rowNum, array_merge([""], $columnsTitle));

                foreach ($data as $key => $item) {
                    $rowNum++;
                    $rowData = [title_case($key)];

                    foreach ($columnsTitle as $key => $title) {
                        if (isset($item[$key + 1])) {
                            $rowData[] = $item[$key + 1];
                        } else {
                            $rowData[] = 0;
                        }
                    }

                    $sheet->row($rowNum, $rowData);

                }

            });

        })->export('xls');
    }

}
