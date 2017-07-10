<?php
namespace App\Services\Chart;

use App\Services\Chart\Contracts\ChartInterface;
use App\Services\Chart\Traits\TimeRangeQuery;
use Carbon\Carbon;
use DB;

class Snaps implements ChartInterface
{
    use TimeRangeQuery;

    protected $currentDate;

    public function __construct()
    {
        // $this->currentDate = Carbon::now();
        $this->currentDate = Carbon::parse('2017-07-03');
    }

    /**
     * Daily users data
     * @return array
     */
    public function daily()
    {
        return [
            'approve' => $this->snaps('daily', 'approve'),
            'pending' => $this->snaps('daily', 'pending'),
            'rejects' => $this->snaps('daily', 'reject'),
        ];
    }

    public function weekly()
    {
        return [
            'approve' => $this->snaps('weekly', 'approve'),
            'pending' => $this->snaps('weekly', 'pending'),
            'rejects' => $this->snaps('weekly', 'reject'),
        ];
    }

    public function monthly()
    {
        return [
            'approve' => $this->snaps('monthly', 'approve'),
            'pending' => $this->snaps('monthly', 'pending'),
            'rejects' => $this->snaps('monthly', 'reject'),
        ];
    }

    public function yearly()
    {
        return [
            'approve' => $this->snaps('yearly', 'approve'),
            'pending' => $this->snaps('yearly', 'pending'),
            'rejects' => $this->snaps('yearly', 'reject'),
        ];
    }

    /**
     * Snaps chart data
     * @param  string     $timeRange  time range of data to summarize. ['daily' | 'weekly' | monthly' | 'yearly']
     * @param  string     $snapType
     * @return @inherit
     */
    protected function snaps($timeRange = 'daily', $status = 'all')
    {
        $query = DB::table('snaps');

        if ($status !== 'all') {
            $query->where('status', '=', $status);
        }

        return $this->buildTimeRangeQuery($timeRange, $query);
    }

}
