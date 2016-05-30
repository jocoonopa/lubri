<?php

namespace App\Export\DailySaleRecord;

use Carbon\Carbon;

class OnlyEcExportHandler extends ExportHandler
{
    /**
     * @override
     */
    protected function sheetTilTodayProc($sheet, $startDate, $endDate)
    {
        return $this
            ->setTargetSheet($sheet)
            ->setSheetBasicStyle()
            ->appendHead()->next()
            ->initDataHelper($startDate, $endDate)
            ->appendErpOuttunnel()
        ;
    }

    /**
     * @override
     */
    protected function toLastOfMonthProc($sheet, $startDate, $endDate)
    {
        return $this
            ->setTargetSheet($sheet)
            ->setSheetBasicStyle()
            ->appendHead()->next()
            ->initDataHelper($startDate, $endDate)
            ->appendErpOuttunnel()
        ;
    }

    /**
     * @override
     */
    protected function todayProc($sheet, $startDate, $endDate)
    {
        return $this
            ->setTargetSheet($sheet)
            ->setSheetBasicStyle()
            ->appendHead()->next()
            ->initDataHelper($startDate, $endDate)
            ->appendErpOuttunnel()
        ;
    }
}