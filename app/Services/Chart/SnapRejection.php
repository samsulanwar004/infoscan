<?php
namespace App\Services\Chart;

use App\Services\Chart\Contracts\ChartInterface;
use App\Services\Chart\Traits\TimeRangeQuery;
use Carbon\Carbon;
use DB;

class SnapRejection implements ChartInterface
{
    use TimeRangeQuery;

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
            'approve' => $this->rejections('daily', 'approve'),
            'pending' => $this->rejections('daily', 'pending'),
            'rejects' => $this->rejections('daily', 'reject'),
        ];
    }

    public function weekly()
    {
        return [
            'approve' => $this->rejections('weekly', 'approve'),
            'pending' => $this->rejections('weekly', 'pending'),
            'rejects' => $this->rejections('weekly', 'reject'),
        ];
    }

    public function monthly()
    {
        return [
            'approve' => $this->rejections('monthly', 'approve'),
            'pending' => $this->rejections('monthly', 'pending'),
            'rejects' => $this->rejections('monthly', 'reject'),
        ];
    }

    public function yearly()
    {
        return [
            'approve' => $this->rejections('yearly', 'approve'),
            'pending' => $this->rejections('yearly', 'pending'),
            'rejects' => $this->rejections('yearly', 'reject'),
        ];
    }

    /**
     * Snaps chart data
     * @param  string     $timeRange  time range of data to summarize. ['daily' | 'weekly' | monthly' | 'yearly']
     * @param  string     $snapType
     * @return @inherit
     */
    protected function rejections($timeRange = 'daily')
    {
        $query = DB::table('snaps')
            ->where('status', '=', 'reject');
        return $this->buildTimeRangeQuery($timeRange, $query);
    }

}
