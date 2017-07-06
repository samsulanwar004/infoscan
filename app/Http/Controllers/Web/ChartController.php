<?php
namespace App\Http\Controllers\Web;

use App\Services\Chart\ActiveUsers;

class ChartController extends AdminController
{

    protected $activeUsers;

    public function __construct(ActiveUsers $activeUsers)
    {
        $this->activeUsers = $activeUsers;
    }

    public function activeUsers()
    {
        $chartData = $this->activeUsers->daily();
        // dd($chartData);
        return $chartData;
    }
}
