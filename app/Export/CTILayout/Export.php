<?php

namespace App\Export\CTILayout;

use App\Utility\Chinghwa\ORM\ERP\HRS_Employee;
use Input;

class Export extends \Maatwebsite\Excel\Files\NewExcelFile 
{
    public function getFilename()
    {
        return 'CTILayout';
    }
}