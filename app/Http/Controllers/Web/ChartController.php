<?php
namespace App\Http\Controllers\Web;

use App\Services\Chart\ActiveUsers;
use App\Services\Chart\SnapRejection;
use App\Services\Chart\Snaps;
use App\Services\Chart\TopTen;
use Carbon\Carbon;
use Excel;
use Illuminate\Http\Request;

class ChartController extends AdminController
{
    protected $activeUsers;
    protected $snaps;
    protected $snapRejection;
    protected $survey;
    protected $topTen;

    public function __construct(
        ActiveUsers $activeUsers,
        Snaps $snaps,
        SnapRejection $snapRejection,
        TopTen $topTen
    ) {
        $this->activeUsers   = $activeUsers;
        $this->snaps         = $snaps;
        $this->snapRejection = $snapRejection;
        $this->topTen        = $topTen;
    }

    public function activeUsers(Request $request, $timeRange = 'daily')
    {
        if (method_exists($this->activeUsers, $timeRange)) {
            $chartData = $this->activeUsers->{$timeRange}();
            $this->toXls($timeRange, $chartData);
            return $chartData;
        } else {
            abort(404);
        }
    }

    protected function toXls($timeRange = 'daily', array $data)
    {

        Excel::create('Statistics data', function ($excel) use ($data) {

            $excel->sheet('Sheetname', function ($sheet) use ($data) {
                $startDate = Carbon::now()->startOfWeek();

                $sheet->cell('A1', function ($cell) {
                    $cell->setValue('Active Users Daily');
                });

                $sheet->cell('A2', function ($cell) use ($startDate) {
                    $cell->setValue('of ' . $startDate->format('d-m-Y') . ' - ' . $startDate->addDays(6)->format('d-m-Y'));
                });

                // set column header as date format
                $sheet->setColumnFormat([
                    'B4:H4' => 'dd-mm-yyyy'
                ]);

                $columnsTitle = ['']; // set empty string as first array item
                $startDate = Carbon::now()->startOfWeek();
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

    public function snapsStatus(Request $request, $timeRange = 'daily')
    {
        if (method_exists($this->snaps, $timeRange)) {
            $chartData = $this->snaps->{$timeRange}();
            return $chartData;
        } else {
            abort(404);
        }
    }

    public function snapsRejections(Request $request, $timeRange = 'daily')
    {
        // if (method_exists($this->snapRejection, $timeRange)) {
        //     $chartData = $this->snapRejection->{$timeRange}();
        //     return $chartData;
        // } else {
        //     abort(404);
        // }
        return $this->snapRejection->summarize($timeRange);

    }

    // public function survey(Request $request, $timeRange = 'daily')
    // {
    //     if (method_exists($this->survey, $timeRange)) {
    //         $chartData = $this->survey->{$timeRange}();
    //         return $chartData;
    //     } else {
    //         abort(404);
    //     }

    // }

    public function topTen(Request $request, $timeRange = 'daily')
    {
        if (method_exists($this->topTen, $timeRange)) {
            $chartData = $this->topTen->{$timeRange}();
            return $chartData;
        } else {
            abort(404);
        }
    }

}
