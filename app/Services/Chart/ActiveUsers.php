<?php
namespace App\Services\Chart;

use App\Services\Chart\Contracts\ChartInterface;
use Carbon\Carbon;
use DB;

class ActiveUsers implements ChartInterface
{

    protected $currentDate;

    public function __construct()
    {
        $this->currentDate = Carbon::now();
    }
    public function daily()
    {
        $newUsers = $this->newUsers('yearly');

        return [
            'new-users' => $newUsers,
        ];
    }

    public function weekly()
    {

    }

    public function monthly()
    {

    }

    public function yearly()
    {

    }

    /**
     * New Users chart data
     * @param    string $timeRange time range to count. 'daily' | 'weekly' | monthly' | 'yearly'
     * @return
     */
    protected function newUsers($timeRange = 'daily')
    {
        $query = DB::table('members');

        switch ($timeRange)
        {
            case 'weekly':
                $query->select(DB::raw('FLOOR((DAYOFMONTH(CURRENT_DATE()) - 1) / 7) + 1 AS week_of_month,
                    WEEK(created_at) AS week, MONTH(created_at) as month,  COUNT(created_at) AS total'))
                    ->groupBy(['month', 'week'])
                    ->having('month', '=', $this->currentDate->month);
                $keyField = 'week_of_month';
                break;

            case 'monthly':
                $query->select(DB::raw('MONTH(created_at) as month, YEAR(created_at) as year, COUNT(created_at) AS total'))
                    ->groupBy(['year', 'month'])
                    ->having('year', '=', $this->currentDate->year);
                $keyField = 'month';
                break;

            case 'yearly':
                $query->select(DB::raw('YEAR(created_at) as year,  COUNT(created_at) AS total'))
                    ->groupBy(['year']);
                $keyField = 'year';
                break;

            default: // daily
                $query->select(DB::raw('WEEKDAY(created_at) AS day, WEEK(created_at) AS week, COUNT(created_at) AS total'))
                    ->groupBy(['week', 'day'])
                    ->having('week', '=', $this->currentDate->weekOfYear);
                $keyField = 'day';

        }

        return collect($query->get())->pluck('total', $keyField);
    }
}
