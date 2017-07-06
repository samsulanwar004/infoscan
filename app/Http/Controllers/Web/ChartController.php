<?php
namespace App\Http\Controllers\Web;

use App\Services\Chart\ActiveUsers;
use Illuminate\Http\Request;

class ChartController extends AdminController
{

    protected $activeUsers;

    public function __construct(ActiveUsers $activeUsers)
    {
        $this->activeUsers = $activeUsers;
    }

    public function activeUsers(Request $request, $timeRange = 'daily')
    {
        if (method_exists($this->activeUsers, $timeRange))
        {
            $chartData = $this->activeUsers->{$timeRange}();
            return $chartData;
        }
        else
        {
            abort(404);
        }
    }
}
