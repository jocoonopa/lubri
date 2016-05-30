<?php

namespace App\Export\DailySaleRecord;

use Carbon\Carbon;

class OnlyCtiExportHandler extends ExportHandler
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
            ->appendByIterateErpTelGroups()->prev()        
            ->appendTotalErpTelGroup()->next()->next()
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
            ->appendByIterateErpTelGroups()->prev()        
            ->appendTotalErpTelGroup()->next()->next()
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
            ->appendByIterateErpTelGroups()->prev()        
            ->appendTotalErpTelGroup()->next()->next()
        ;   
    }
}