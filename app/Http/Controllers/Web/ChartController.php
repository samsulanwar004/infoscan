<?php
namespace App\Http\Controllers\Web;

use App\Services\Chart\ActiveUsers;
use App\Services\Chart\Snaps;
use Illuminate\Http\Request;

class ChartController extends AdminController
{
    protected $activeUsers;

    protected $snaps;

    public function __construct(
        ActiveUsers $activeUsers,
        Snaps $snaps
    ) {
        $this->activeUsers = $activeUsers;
        $this->snaps       = $snaps;
    }

    public function activeUsers(Request $request, $timeRange = 'daily')
    {
        if (method_exists($this->activeUsers, $timeRange)) {
            $chartData = $this->activeUsers->{$timeRange}();
            return $chartData;
        } else {
            abort(404);
        }
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

}
