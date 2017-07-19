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

        BaseExcel::create('Chart data', function ($excel) use ($data, $timeRange) {

            $excel->sheet('Sheetname', function ($sheet) use ($data, $timeRange) {
                $startDate = Carbon::now()->startOfWeek();

                $sheet->cell('A1', function ($cell) use ($timeRange) {
                    $cell->setValue($this->title . ' ' . ucfirst($timeRange));
                });

                $sheet->cell('A2', function ($cell) use ($startDate, $timeRange) {
                    $cell->setValue('of ' . $this->getTableSubtitle($timeRange));
                });

                if ($timeRange == 'daily') {
                    // set column header as date format
                    $sheet->setColumnFormat([
                        'B4:H4' => 'dd-mm-yyyy',
                    ]);
                }

                $rowNum = 4;

                $columnsTitle =  $this->columnsTitle($timeRange);
                $sheet->row($rowNum, array_merge([''], $columnsTitle));
                foreach ($data as $key => $item) {
                    $rowNum++;
                    $rowData = [str_replace('-', ' ', title_case($key))];

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

    protected function getTableSubtitle($timeRange)
    {
        $startOfWeek = Carbon::now()->startOfWeek();

        switch ($timeRange) {
            case 'weekly':
                return $startOfWeek->format('F Y');
                break;
           case 'monthly':
                return $startOfWeek->format('Y');
                break;
            case 'yearly':
                return 'All Period';
                break;
            default: // daily
                return $startOfWeek->format('d-m-Y') . ' - ' . $startOfWeek->addDays(6)->format('d-m-Y');
                break;
        }
    }

    protected function columnsTitle($timeRange){
        switch ($timeRange) {
            case 'weekly':
               return [
                    'Week 1',
                    "Week 2",
                    "Week 3",
                    "Week 4",
                    "Week 5"
                ];
                break;
            case 'monthly':
                return [
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
                break;
            case 'yearly':
                return [];
                break;

            default: // daily
                $startDate    = Carbon::now()->startOfWeek();
                for ($i = 0; $i < 7; $i++) {
                    $columnsTitle[] = $startDate->format('d-m-Y');
                    $startDate->addDay();
                }
                break;
        }
    }


}
