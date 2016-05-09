<?php

namespace App\Http\Controllers\Flap\CCS_OrderDivIndex;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Utility\Chinghwa\Database\Query\Processors\Processor;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use Input;

class FindDivController extends Controller
{
    protected $startKeyInDate;
    protected $endKeyInDate;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('flap.ccsorderdivindex.finddiv.index', [
            'orders' => $this->find(),
            'start' => $this->startKeyInDate,
            'end' => $this->endKeyInDate
        ]);
    }

    protected function find()
    {
        $this->startKeyInDate = Input::get('start');
        $this->endKeyInDate = Input::get('end');

        if (!empty($this->startKeyInDate) && !empty($this->endKeyInDate)) {
            if ($this->endKeyInDate < $this->startKeyInDate) {
                $tmp = $this->startKeyInDate;
                $this->startKeyInDate = $this->endKeyInDate;
                $this->endKeyInDate = $tmp;
            }
        }   

        $q = Processor::table('CCS_OrderDivIndex WITH(NOLOCK)')
            ->select(DB::raw("MAX(CCS_OrderIndex.OrderNo) AS 單號,MAX(CCS_OrderIndex.KeyInDate) AS 出貨日,MAX(CCS_OrderIndex.OrderDate) AS 訂單日,MAX(CCS_OrderDivIndex.IndexSerNo) AS 流水號,MAX(CCS_OrderIndex.MustPayTotal) AS 應付帳款, MAX(POS_Member.Name) AS 會員姓名,COUNT(*) AS 分寄單數, MAX(CCS_OrderIndex.VerifyDate) AS 覆核日, MAX(CCS_OrderIndex.Status) AS 狀態"))
            ->leftJoin('CCS_OrderIndex WITH(NOLOCK)', 'CCS_OrderIndex.SerNo', '=', 'CCS_OrderDivIndex.IndexSerNo')
            ->leftJoin('POS_Member WITH(NOLOCK)', 'POS_Member.SerNo', '=', 'CCS_OrderIndex.MemberSerNo')
            ->groupBy('CCS_OrderDivIndex.IndexSerNo')
            ->having('COUNT(*)', '>', 1)
            // ->orWhere(function ($query) {
            //     $query
            //         ->having('COUNT(*)', '=', 1)
            //         ->where('CCS_OrderDivIndex.town', '=', '新店區')
            //         ->where('CCS_OrderDivIndex.city', '=', '新北市')
            //         ->where('CCS_OrderDivIndex.address', 'like', '寶強路6%')
            //     ;
            // })
            ->orderBy('MAX(CCS_OrderIndex.KeyInDate)')
        ;

        if (!empty($this->startKeyInDate)) {            
            $q->where('CCS_OrderIndex.KeyInDate', '>=', $this->startKeyInDate);
        }
        
        if (!empty($this->endKeyInDate)) {
            $q->where('CCS_OrderIndex.KeyInDate', '<=', $this->endKeyInDate);
        }

        if (empty($this->startKeyInDate) && empty($this->endKeyInDate)) {
            $this->startKeyInDate = Carbon::now()->format('Ymd');
            $this->endKeyInDate = Carbon::now()->format('Ymd');

            $q->where('CCS_OrderIndex.KeyInDate', '>=', $this->startKeyInDate);
            $q->where('CCS_OrderIndex.KeyInDate', '<=', $this->endKeyInDate);
        }

        dd(Processor::toSql($q));

        return Processor::getArrayResult($q);
    }
}
