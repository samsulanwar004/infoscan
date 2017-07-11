<?php
namespace App\Services\Chart;

use App\Services\Chart\Contracts\ChartInterface;
use App\Services\Chart\Traits\TimeRangeQuery;
use Carbon\Carbon;
use DB;

class ActiveUsers implements ChartInterface
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
            'new-users'     => $this->newUsers('daily'),
            'snaps'         => $this->snaps('daily'),
            'receipts'      => $this->snaps('daily', 'receipt'),
            'general-trade' => $this->snaps('daily', 'generalTrade'), // Warung
            'hand-written'  => $this->snaps('daily', 'handWritten'), // Nota tulis
        ];
    }

    public function weekly()
    {
        return [
            'new-users'     => $this->newUsers('weekly'),
            'snaps'         => $this->snaps('weekly'),
            'receipts'      => $this->snaps('weekly', 'receipt'),
            'general-trade' => $this->snaps('weekly', 'generalTrade'), // Warung
            'hand-written'  => $this->snaps('weekly', 'handWritten'), // Nota tulis
        ];
    }

    public function monthly()
    {
        return [
            'new-users'     => $this->newUsers('monthly'),
            'snaps'         => $this->snaps('monthly'),
            'receipts'      => $this->snaps('monthly', 'receipt'),
            'general-trade' => $this->snaps('monthly', 'generalTrade'), // Warung
            'hand-written'  => $this->snaps('monthly', 'handWritten'), // Nota tulis
        ];
    }

    public function yearly()
    {
        return [
            'new-users'     => $this->newUsers('yearly'),
            'snaps'         => $this->snaps('yearly'),
            'receipts'      => $this->snaps('yearly', 'receipt'),
            'general-trade' => $this->snaps('yearly', 'generalTrade'), // Warung
            'hand-written'  => $this->snaps('yearly', 'handWritten'), // Nota tulis
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

        if ($snapType !== 'all') {
            $query->where('snap_type', '=', $snapType);
        }

        return $this->buildTimeRangeQuery($timeRange, $query);
    }

}
