<?php

namespace App\Http\Controllers\Flap\CCS_OrderDivIndex;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Utility\Chinghwa;
use App\Utility\Chinghwa\Database\Query\Processors\Processor;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
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
            ->select(DB::raw("MAX(CCS_OrderIndex.OrderNo) AS 單號,MAX(CCS_OrderIndex.KeyInDate) AS 出貨日,MAX(CCS_OrderIndex.OrderDate) AS 訂單日,MAX(CCS_OrderDivIndex.IndexSerNo) AS 流水號,MAX(CCS_OrderIndex.MustPayTotal) AS 應付帳款, MAX(POS_Member.Name) AS 會員姓名,COUNT(*) AS 分寄單數, MAX(CCS_OrderIndex.VerifyDate) AS 覆核日, MAX(CCS_OrderIndex.Status) AS 狀態, MAX(CCS_OrderDivIndex.City) AS 縣市, MAX(CCS_OrderDivIndex.Town) AS 區, MAX(CCS_OrderDivIndex.Address) AS 地址"))
            ->leftJoin('CCS_OrderIndex WITH(NOLOCK)', 'CCS_OrderIndex.SerNo', '=', 'CCS_OrderDivIndex.IndexSerNo')
            ->leftJoin('POS_Member WITH(NOLOCK)', 'POS_Member.SerNo', '=', 'CCS_OrderIndex.MemberSerNo')
            ->groupBy('CCS_OrderDivIndex.IndexSerNo')           
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

        $result = Processor::getArrayResult($q);

        return array_values($this->filter($result));

        //return array_values(array_filter(Processor::getArrayResult($q), [$this, 'filter']));
    }

    protected function filter($result)
    {
        foreach ($result as $key => $var) {
            $result[$key]['isAddressError'] = false;
        
            if (1 < $var['分寄單數']) {
                continue;
            }

            if (1 === (int) $var['分寄單數'] 
                && Chinghwa::CITY === trim(keepOnlyChineseWord($var['縣市'])) 
                && Chinghwa::TOWN === trim(keepOnlyChineseWord($var['區'])) 
                && false !== strpos(trim(keepOnlyChineseWord($var['地址'])), '寶強路')
            ) {
                $result[$key]['isAddressError'] = true;

                continue;
            } 

            if (0 === (int) $var['應付帳款']) {
                continue;
            }

            unset($result[$key]);

            continue;
        }

        return $result;
    }
}
