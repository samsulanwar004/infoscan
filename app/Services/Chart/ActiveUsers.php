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

    /**
     * Daily users data
     * @return array
     */
    public function daily()
    {
        return [
            'new-users'     => $this->newUsers('daily'),
            'snaps'         => $this->snaps('daily'),
            'receipts'      => $this->snaps('daily', 'receipt'),
            'general-trade' => $this->snaps('daily', 'generalTrade'), // Warung
            'hand-writen'   => $this->snaps('daily', 'handWritten'), // Nota tulis
        ];
    }

    public function weekly()
    {
        return [
            'new-users'     => $this->newUsers('weekly'),
            'snaps'         => $this->snaps('weekly'),
            'receipts'      => $this->snaps('weekly', 'receipt'),
            'general-trade' => $this->snaps('weekly', 'generalTrade'), // Warung
            'hand-writen'   => $this->snaps('weekly', 'handWritten'), // Nota tulis
        ];
    }

    public function monthly()
    {
        return [
            'new-users'     => $this->newUsers('monthly'),
            'snaps'         => $this->snaps('monthly'),
            'receipts'      => $this->snaps('monthly', 'receipt'),
            'general-trade' => $this->snaps('monthly', 'generalTrade'), // Warung
            'hand-writen'   => $this->snaps('monthly', 'handWritten'), // Nota tulis
        ];
    }

    public function yearly()
    {
        return [
            'new-users'     => $this->newUsers('yearly'),
            'snaps'         => $this->snaps('yearly'),
            'receipts'      => $this->snaps('yearly', 'receipt'),
            'general-trade' => $this->snaps('yearly', 'generalTrade'), // Warung
            'hand-writen'   => $this->snaps('yearly', 'handWritten'), // Nota tulis
        ];
    }

    /**
     * New Users chart data
     * @param  string     $timeRange time range of data to summarize. ['daily' | 'weekly' | monthly' | 'yearly']
     * @return @inherit
     */
    protected function newUsers($timeRange = 'daily')
    {
        $query = DB::table('members');
        return $this->buildTimeRangeQuery($timeRange, $query);
    }

    /**
     * Snaps chart data
     * @param  string     $timeRange  time range of data to summarize. ['daily' | 'weekly' | monthly' | 'yearly']
     * @param  string     $snapType
     * @return @inherit
     */
    protected function snaps($timeRange = 'daily', $snapType = 'all')
    {
        $query = DB::table('snaps');

        if ($snapType !== 'all')
        {
            $query->where('snap_type', '=', $snapType);
        }

        return $this->buildTimeRangeQuery($timeRange, $query);
    }

    /**
     * Building query based on time range (daily, weekly, monthly, yearly)
     * @param  string                            $timeRange
     * @param  Illuminate\Database\Query\Builder $query
     * @return Illuminate\Support\Collection
     */
    protected function buildTimeRangeQuery($timeRange, $query)
    {
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

        return collect($query->get())
            ->pluck('total', $keyField);
    }
}
