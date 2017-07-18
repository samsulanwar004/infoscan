<?php
namespace App\Http\Controllers\Web;

use App\Services\Chart\ActiveUsers;
use App\Services\Chart\Excel;
use App\Services\Chart\SnapRejection;
use App\Services\Chart\Snaps;
use App\Services\Chart\TopTen;
use Illuminate\Http\Request;

class ChartController extends AdminController
{
    protected $activeUsers;
    protected $snaps;
    protected $snapRejection;
    protected $survey;
    protected $topTen;
    protected $excel;

    public function __construct(
        ActiveUsers $activeUsers,
        Snaps $snaps,
        SnapRejection $snapRejection,
        TopTen $topTen,
        Excel $excel
    ) {
        $this->activeUsers   = $activeUsers;
        $this->snaps         = $snaps;
        $this->snapRejection = $snapRejection;
        $this->topTen        = $topTen;
        $this->excel         = $excel;
    }

    public function activeUsers(Request $request, $timeRange = 'daily', $export = false)
    {
        if (method_exists($this->activeUsers, $timeRange)) {
            $chartData = $this->activeUsers->{$timeRange}();

            if ($export == 'xls') {
                return $this->excel->export('Active Users', $timeRange, $chartData);
            }

            return $chartData;
        } else {
            abort(404);
        }
    }

    public function snapsStatus(Request $request, $timeRange = 'daily', $export = false)
    {
        if (method_exists($this->snaps, $timeRange)) {
            $chartData = $this->snaps->{$timeRange}();

            if ($export == 'xls') {
                return $this->excel->export('Snaps Statuses', $timeRange, $chartData);
            }

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
