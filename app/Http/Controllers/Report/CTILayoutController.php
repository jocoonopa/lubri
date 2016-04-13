<?php

namespace App\Http\Controllers\Report;

use App\Export\CTILayout\Export;
use App\Http\Controllers\Controller;
use Input;

class CTILayoutController extends Controller
{
	public function index(Export $export)
    {
        if (NULL === Input::get('code')) {
            return 'Please give a code param with url, like <a href="http://localhost.lubri_dev/report/ctilayout?code=20160202">http://localhost.lubri_dev/report/ctilayout?code=20160202</a>' ;
        }

        set_time_limit(0);
        
        $export->handleExport();
    }
}