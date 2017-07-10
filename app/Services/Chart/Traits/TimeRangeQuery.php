<?php
namespace App\Services\Chart\Traits;

use DB;

trait TimeRangeQuery
{
    /**
     * Building query based on time range (daily, weekly, monthly, yearly)
     * @param  string                            $timeRange
     * @param  Illuminate\Database\Query\Builder $query
     * @return Illuminate\Support\Collection
     */
    protected function buildTimeRangeQuery($timeRange, $query)
    {
        switch ($timeRange) {
            case 'weekly':
                $query->select(
                    DB::raw(
                        'FLOOR((DAYOFMONTH(CURRENT_DATE()) - 1) / 7) + 1 AS week_of_month, WEEK(created_at) AS week, ' .
                        'MONTH(created_at) as month,  COUNT(created_at) AS total'
                    ))
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
