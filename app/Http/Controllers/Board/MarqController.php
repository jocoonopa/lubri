<?php

namespace App\Http\Controllers\Board;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Utility\Chinghwa\Database\Query\Processors\Processor;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Input;
use Redirect;

class MarqController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $data = Processor::getArrayResult($this->getMarqQuery());

            $count = count($data);
            $offset = Input::get('offset', 0);
            $offset = ($offset > ($count + 5)) ? 0 : $offset;

            $data = array_slice($data, $offset, 8);

            return ($offset < $count) ? view('board.marq.index', ['data' => $data, 'offset' => ($offset + 5)]) : Redirect::to('board/marq/group?timeout=' . Input::get('timeout', 10));
        } catch (\Exception $e) {
            return view('board.marq.index', ['data' => [], 'offset' => Input::get('offset', 0)]);
        }
    }

    protected function getOffset($count)
    {
        $offset = Input::get('offset', 0);

        return $offset = ($offset > ($count + 5)) ? 0 : $offset;
    }

    public function group()
    {
        return view('board.marq.group', ['data' => $this->extendGroupSum(Processor::getArrayResult($this->getMarqGroupQuery()))]);
    }

    protected function extendGroupSum($data)
    {    
        $today = 0;
        $thisWeek = 0;
        $thisMonth = 0;

        foreach ($data as $row) {
            $today += array_get($row, '今日業績', 0);
            $thisWeek += array_get($row, '本周業績', 0);
            $thisMonth += array_get($row, '本月累計', 0);
        }

        $data[] = ['部門' => '總計', '今日業績' => $today, '本周業績' => $thisWeek, '本月累計' => $thisMonth];

        return $data;
    }

    protected function getMarqQuery()
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

    protected function getMarqGroupQuery()
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
            Processor::getStorageSql('Board/Marq/record_group_corp.sql')
        );
    }
}
