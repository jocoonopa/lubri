<?php

namespace App\Http\Controllers\Board;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Utility\Chinghwa\Database\Query\Processors\Processor;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Input;

class MarqController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Processor::getArrayResult($this->getQuery());

        $count = count($data);
        $offset = Input::get('offset', -5) + 5;
        $offset = ($offset > $count) ? 0 : $offset;

        $data = array_slice($data, $offset, 8);

        return view('board.marq.index', ['data' => $data, 'offset' => $offset]);
    }

    protected function getQuery()
    {
        $startOfMonth = Carbon::now()->modify('first day of this month')->format('Ymd');
        $today = Carbon::now()->format('Ymd');

        return str_replace(
            ['$whereCondition', '$today', '$weekStart', '$weekEnd'],
            [
                "CCS_OrderIndex.Status = 1 AND CCS_OrderIndex.KeyInDate BETWEEN $startOfMonth AND $today AND FAS_Corp.Code IN('CH53000','CH54000','CH54100')", 
                Carbon::now()->format('Ymd'), 
                Carbon::now()->startOfWeek()->format('Ymd'), 
                Carbon::now()->endOfWeek()->format('Ymd')
            ],
            Processor::getStorageSql('Board/Marq/record.sql')
        );
    }
}
