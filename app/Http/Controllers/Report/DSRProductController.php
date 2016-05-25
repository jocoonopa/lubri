<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Utility\Chinghwa\Database\Query\Processors\Processor;

class DSRProductController extends Controller
{
    public function index()
    {   
        dd(Processor::getArrayResult("select * FROM [dbo].[lubri_DSR_Product]('20160522', '20160531')"));
    }
}