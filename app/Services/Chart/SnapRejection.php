<?php
namespace App\Services\Chart;

use Carbon\Carbon;
use DB;
use App\Setting;

class SnapRejection
{
    protected $currentDate;

    public function __construct()
    {
        $this->currentDate = Carbon::now();
    }

    public function summarize($timeRange = 'daily')
    {
        $reasons = Setting::where('setting_group', '=', 'snap_reason')->get();
        $summary = [];
        foreach ($reasons as $reason)
        {
            $summary[str_slug($reason->setting_value)] = $this->rejectionByCode($timeRange, $reason->setting_name);
        }

        return $summary;
    }

    protected function rejectionByCode($timeRange = 'daily', $rejectionCode)
    {
        $query = DB::table('snaps')
            ->where('status', '=', 'reject')
            ->where('rejection_code', '=', $rejectionCode);
        return $this->buildTimeRangeQuery($timeRange, $query, 'snaps.updated_at');
    }

    /**
     * Building query based on time range (daily, weekly, monthly, yearly)
     * @param  string                            $timeRange
     * @param  Illuminate\Database\Query\Builder $query
     * @return Illuminate\Support\Collection
     */
    protected function buildTimeRangeQuery($timeRange, $query, $dateTimeField = 'created_at')
    {
        switch ($timeRange) {
            case 'weekly':
                $query->addSelect(
                    DB::raw(
                        'FLOOR((DAYOFMONTH(CURRENT_DATE()) - 1) / 7) + 1 AS week_of_month, WEEK(' . $dateTimeField . ') AS week, ' .
                        'YEAR(' . $dateTimeField . ') as year, MONTH(' . $dateTimeField . ') as month,  COUNT(' . $dateTimeField . ') AS total'
                    ))
                    ->groupBy(['year', 'month', 'week'])
                    ->having('month', '=', $this->currentDate->month);
                $keyField = 'week_of_month';
                break;

            case 'monthly':
                $query->addSelect(DB::raw('MONTH(' . $dateTimeField . ') as month, YEAR(' . $dateTimeField . ') as year, COUNT(' . $dateTimeField . ') AS total'))
                    ->groupBy(['year', 'month'])
                    ->having('year', '=', $this->currentDate->year);
                $keyField = 'month';
                break;

            case 'yearly':
                $query->addSelect(DB::raw('YEAR(' . $dateTimeField . ') as year,  COUNT(' . $dateTimeField . ') AS total'))
                    ->groupBy(['year']);
                $keyField = 'year';
                break;

            default: // daily
                $query->addSelect(DB::raw('YEAR(' . $dateTimeField . ') as year, WEEKDAY(' . $dateTimeField . ') AS day, WEEK(' . $dateTimeField . ') AS week, COUNT(' . $dateTimeField . ') AS total'))
                    ->groupBy(['year', 'week', 'day'])
                    ->having('week', '=', $this->currentDate->weekOfYear);
                $keyField = 'day';

        }

        return $query->get()
            ->pluck('total', $keyField);
    }

}
