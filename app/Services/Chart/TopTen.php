<?php
namespace App\Services\Chart;

use App\Services\Chart\Contracts\ChartInterface;
use App\Services\Chart\Traits\TimeRangeQuery;
use Carbon\Carbon;
use DB;

class TopTen implements ChartInterface
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
            'claims-reason' => $this->claimReason('daily'),
            // 'user-approved-claims' => $this->snaps('daily', 'pending'),
            // 'rejections' => $this->snaps('daily', 'reject'),
            // 'user-rejected-claims' => $this->snaps()
        ];
    }

    public function weekly()
    {
        return [
            'claims-reason' => $this->claimReason('weekly'),
            // 'user-approved-claims' => $this->snaps('daily', 'pending'),
            // 'rejections' => $this->snaps('daily', 'reject'),
            // 'user-rejected-claims' => $this->snaps()
        ];
    }

    public function monthly()
    {
        return [
            'claims-reason' => $this->claimReason('monthly'),
            // 'user-approved-claims' => $this->snaps('daily', 'pending'),
            // 'rejections' => $this->snaps('daily', 'reject'),
            // 'user-rejected-claims' => $this->snaps()
        ];
    }

    public function yearly()
    {
        return [
            'claims-reason' => $this->claimReason('yearly'),
            // 'user-approved-claims' => $this->snaps('daily', 'pending'),
            // 'rejections' => $this->snaps('daily', 'reject'),
            // 'user-rejected-claims' => $this->snaps()
        ];
    }

    protected function claimReason($timeRange = 'daily')
    {
        $query = DB::table('snaps')
            ->join('settings', 'snaps.rejection_code', '=', 'settings.setting_name')
            ->select(DB::raw('settings.setting_value as label, count(rejection_code) as total, rejection_code'))
            ->whereNotNull('rejection_code')
            ->where('status', '=', 'reject')
            ->groupBy('rejection_code');

        return $this->buildTimeRangeQuery($timeRange, $query, 'settings.updated_at');
    }

    /**
     * Building query based on time range (daily, weekly, monthly, yearly)
     * @param  string                            $timeRange
     * @param  Illuminate\Database\Query\Builder $query
     * @return Illuminate\Support\Collection
     */
    protected function buildTimeRangeQuery($timeRange, $query, $dateTimeFieldName = 'created_at')
    {
        switch ($timeRange) {
            case 'weekly':
                $query->addSelect(
                    DB::raw(
                        'FLOOR((DAYOFMONTH(CURRENT_DATE()) - 1) / 7) + 1 AS week_of_month, WEEK(' . $dateTimeFieldName . ') AS week, ' .
                        'MONTH(' . $dateTimeFieldName . ') as month,  COUNT(' . $dateTimeFieldName . ') AS total'
                    ))
                    ->groupBy(['month', 'week'])
                    ->having('month', '=', $this->currentDate->month);
                $keyField = 'week_of_month';
                break;

            case 'monthly':
                $query->addSelect(DB::raw('MONTH(' . $dateTimeFieldName . ') as month, YEAR(' . $dateTimeFieldName . ') as year, COUNT(' . $dateTimeFieldName . ') AS total'))
                    ->groupBy(['year', 'month'])
                    ->having('year', '=', $this->currentDate->year);
                $keyField = 'month';
                break;

            case 'yearly':
                $query->addSelect(DB::raw('YEAR(' . $dateTimeFieldName . ') as year,  COUNT(' . $dateTimeFieldName . ') AS total'))
                    ->groupBy(['year']);
                $keyField = 'year';
                break;

            default: // daily
                $query->addSelect(DB::raw('WEEKDAY(' . $dateTimeFieldName . ') AS day, WEEK(' . $dateTimeFieldName . ') AS week, COUNT(' . $dateTimeFieldName . ') AS total'))
                    ->groupBy(['week', 'day'])
                    ->having('week', '=', $this->currentDate->weekOfYear);
                $keyField = 'day';

        }

        return $query->get();

    }

}
