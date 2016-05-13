<?php

namespace App\Export\DailySaleRecord;

use Carbon\Carbon;

class OnlyEcExportHandler extends ExportHandler
{
    /**
     * @override
     */
    protected function getSheetToLastOfMonthFunc()
    {
        return function ($sheet) {
            $startDate = with(new Carbon('first day of this month'))->format('Ymd');
            $endDate = with(new Carbon('last day of this month'))->format('Ymd');

            $this
                ->setTargetSheet($sheet)
                ->setSheetBasicStyle()
                ->appendHead()->next()
                ->initDataHelper($startDate, $endDate)
                ->appendErpOuttunnel()
            ;   
       };
    }

    /**
     * @override
     */
    protected function getSheetTodayFunc()
    {
        return function ($sheet) {
            $this->date = Carbon::now()->modify('-1 days');
            $this->rowIndex = 1;
            $startDate = Carbon::now()->modify('-1 days')->format('Ymd');
            $endDate = $startDate;

            $this
                ->setTargetSheet($sheet)
                ->setSheetBasicStyle()
                ->appendHead()->next()
                ->initDataHelper($startDate, $endDate)
                ->appendErpOuttunnel()
            ;   
       };
    }

    /**
     * @override
     */
    protected function getSheetTilTodayFunc()
    {
        return function ($sheet) {
            $this->date = Carbon::now()->modify('-1 days');
            $this->rowIndex = 1;

            $startDate = with(new Carbon('first day of this month'))->format('Ymd');
            $endDate = Carbon::now()->modify('-1 days')->format('Ymd');

            $this
                ->setTargetSheet($sheet)
                ->setSheetBasicStyle()
                ->appendHead()->next()
                ->initDataHelper($startDate, $endDate)
                ->appendErpOuttunnel()
            ;   
       };
    }
}