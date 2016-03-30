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

            $offset += 5;

            return ($offset < ($count + 5)) ? view('board.marq.index', ['data' => $data, 'offset' => $offset]) : Redirect::to('board/marq/group?timeout=' . Input::get('timeout', 10));
        } catch (\Exception $e) {
            return view('board.marq.index', ['data' => [], 'offset' => Input::get('offset', 0)]);
        }
    }

    public function group()
    {
        return view('board.marq.group', ['data' => Processor::getArrayResult($this->getMarqGroupQuery())]);
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
