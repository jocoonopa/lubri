<?php

namespace App\Export\CTILayout;

/**
 * @deprecated [<20160712>] [<No more need to use this class>]
 */
class CtiExport extends \Maatwebsite\Excel\Files\NewExcelFile 
{
    public function getFilename()
    {
        return 'CTILayout_CtiExport';
    }
}