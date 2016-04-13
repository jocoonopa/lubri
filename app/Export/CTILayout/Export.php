<?php

namespace App\Export\CTILayout;

use App\Utility\Chinghwa\ORM\ERP\HRS_Employee;
use Input;

class Export extends \Maatwebsite\Excel\Files\NewExcelFile 
{
    public function getFilename()
    {
        $emp = HRS_Employee::first(['code' => Input::get('code', '20160203')]);

        return 'CTILayout_' . array_get($emp, 'Code') . '_' . array_get($emp, 'Name');
    }
}