<?php

namespace App\Http\Controllers\Flap;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Utility\Chinghwa\Database\Query\Processors\Processor;
use Carbon\Carbon;
use DB;
use Excel;
use PHPExcel_Cell;
use Illuminate\Http\Request;

class ManagerController extends Controller
{
    /**
     * Display a listing of the resource.
     * 
     * Reference: http://bl.ocks.org/mbostock/3884955
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {        
        $socket = fsockopen("ptt.cc", 23);

        if(!$socket)return;
        stream_set_blocking($socket, 0);
        stream_set_blocking(\STDIN, 0);

        do {
          echo "$ ";
          $read = array($socket, \STDIN); 
          $write  = NULL; 
          $except = NULL;

          if(!is_resource($socket)) return;
          $num_changed_streams = @stream_select($read, $write, $except, null);
          if(feof($socket)) return ;


          if($num_changed_streams  === 0) continue;
          if (false === $num_changed_streams) {
              /* Error handling */
            var_dump($read);
            echo "Continue\n";
            die;
          } elseif ($num_changed_streams > 0) {
            echo "\r";
            $data = fread($socket, 4096);
            if($data !== "") 
              echo "<<< $data";

            $data2 = fread(\STDIN, 4096);

            if($data2 !== "") {
              echo ">>> $data2";
              fwrite($socket, trim($data2));
            }
          }

        } while(true);
        dd(123);
        Excel::create('manager', function($excel) {            
            $corpCodes = ['CH53000', 'CH54000', 'CH54100'];
            $corpCodes[] = '合計';

            foreach ($corpCodes as $corpCode) {
                $excel->sheet($this->getCorpName($corpCode), function ($sheet) use ($corpCodes, $corpCode) {
                    $productCodes = ['A00021','A00034','A00047','A00100','A00267','A00286','A00422','A00438','A00458','A00459','A00460','A00461','A00463','A00473','A00474','A00482','A00486','A00490','A00491','A00492','A00493','A00495','A00497','A00499','A00500','A00506','A00513','A00519','A00520','A00537','A00539','A00540','A00541','A00542'];
                    $range = ['20140101', '20151231'];
                    $qty = $this->genQtySchema($productCodes);

                    $rows   = [];
                    $heads = array_merge(['date'], $productCodes);
                    $start  = Carbon::instance(new \DateTime(array_get($range, 0)));
                    $end    = Carbon::instance(new \DateTime(array_get($range, 1)));
                    $diff   = $start->diff($end);

                    for ($i = 0; $i <= ($diff->y*12 + $diff->m); $i ++) {
                        $startAfter = $start->copy()->addMonths($i)->modify('first day of this month');
                        $endAfter = $startAfter->copy()->addMonth()->subSecond();
                        $row = [$startAfter->format('Ym')];  
                        
                        if ($this->isFinalTotal($corpCode)) {
                            // B2 -> PHPExcel_Cell::stringFromColumnIndex(2) . 2;
                            // =+客戶經營一部!C3+客戶經營二部!C3+客戶經營三部!C3                                                        
                            for ($j = 1; $j <= count($productCodes); $j ++) {
                                $theIndex = ($i + 2);
                                $formulaString = '=';

                                foreach ($corpCodes as $corpCode) {
                                    if (!$this->isFinalTotal($corpCode)) {
                                        $formulaString .= "+{$this->getCorpName($corpCode)}!" . PHPExcel_Cell::stringFromColumnIndex($j) ."{$theIndex}"; 
                                    }                                
                                }

                                $row[] = $formulaString;
                            }                            
                        } else {        
                            //dd(Processor::toSql($this->getQuery($productCodes, [$startAfter, $endAfter], [$corpCode])));
                            $data = Processor::getArrayResult($this->getQuery($productCodes, [$startAfter, $endAfter], [$corpCode]));                           
                            foreach ($data as $val) {
                                $row[] = array_get($val, 'record');

                                if (array_key_exists(array_get($val, 'Code'), $qty)) {
                                    $qty[array_get($val, 'Code')] += array_get($val, 'qty');
                                }                                
                            }                                                  
                        }    

                        $rows[] = $row;                                                               
                    }

                    $row = ['數量'];

                    if (!$this->isFinalTotal($corpCode)) {                        
                        foreach ($qty as $eachQty) {
                            $row[] = $eachQty;
                        }
                    } else {
                        $theIndex ++;
                        
                        for ($j = 1; $j <= count($productCodes); $j ++) {
                            $formulaString = '=';
                            
                            foreach ($corpCodes as $corpCode) {
                                if (!$this->isFinalTotal($corpCode)) {
                                    $formulaString .= "+{$this->getCorpName($corpCode)}!" . PHPExcel_Cell::stringFromColumnIndex($j) ."{$theIndex}"; 
                                }
                            }

                            $row[] = $formulaString;
                        }
                    }

                    $rows[] = $row;    
                    
                    $sheet->loadView('excel.flap.manager.salerecordByProduct', [
                        'rows' => $rows,
                        'heads' => $heads
                    ]);
                });
            }
            
        })->export();
        //->store('csv', storage_path('excel/exports'));

        return view('flap.manager.index');
    }

    protected function genQtySchema(array $pCodes)
    {
        $qty = [];

        foreach ($pCodes as $code) {
            $qty[$code] = 0;
        }

        return $qty;
    }

    protected function getCorpName($corpCode)
    {
        return $this->isFinalTotal($corpCode) ? $corpCode : array_get(Processor::getArrayResult("SELECT Name FROM FAS_Corp WHERE Code='{$corpCode}'"), '0.Name');
    }

    protected function isFinalTotal($corpCode) {
        return '合計' === $corpCode;
    }

    protected function getQuery($goodsCodes, $dateRange, $corpCodes)
    {
        $sub = Processor::table('PIS_Goods WITH(NOLOCK)')
            ->select(DB::raw(
                'PIS_Goods.Code AS Code, 
                MAX(PIS_Goods.Name) AS Name, 
                MAX(FAS_Corp.Name) AS corpName,
                SUM(CCS_OrderDetails.SubTotal) AS total,
                SUM(CCS_ReturnGoodsD.SubTotal) AS ReturnTotal, 
                SUM(CCS_OrderDetails.Qty) AS Qty, 
                SUM(CCS_ReturnGoodsD.ReturnQty) AS ReturnQty'
            ))
            ->leftJoin('CCS_OrderDetails WITH (NOLOCK)', 'PIS_Goods.SerNo', '=', 'CCS_OrderDetails.GoodsSerNo')
            ->leftJoin('CCS_OrderIndex WITH (NOLOCK)', 'CCS_OrderIndex.SerNo', '=', 'CCS_OrderDetails.IndexSerNo')
            ->leftJoin('FAS_Corp WITH(NOLOCK)', 'FAS_Corp.SerNo', '=', 'CCS_OrderIndex.DeptSerNo')
            ->leftJoin('CCS_ReturnGoodsI WITH (NOLOCK)', 'CCS_OrderIndex.SerNo', '=', 'CCS_ReturnGoodsI.OrderIndexSerNo')
            ->leftJoin('CCS_ReturnGoodsD WITH(NOLOCK)', function ($join) {
                $join
                    ->on('CCS_ReturnGoodsI.SerNo', '=', 'CCS_ReturnGoodsD.IndexSerNo')
                    ->on('CCS_ReturnGoodsD.GoodsSerNo', '=', 'PIS_Goods.SerNo')
                ;
            })
        ;

        if (!empty($goodsCodes)) {
            $sub->whereIn('PIS_Goods.Code', $goodsCodes);
        }

        if (!empty($dateRange)) {
            $sub
                ->where('CCS_OrderIndex.KeyInDate', '>=', array_get($dateRange, 0)->format('Ymd'))
                ->where('CCS_OrderIndex.KeyInDate', '<=', array_get($dateRange, 1)->format('Ymd'))
            ;
        }

        if (!empty($corpCodes)) {
            $sub->whereIn('FAS_Corp.Code', $corpCodes);
        }

        $sub
            ->where('CCS_OrderIndex.Status', '=', 1)
            ->where('CCS_OrderDetails.SubTotal', '>' , 0)
            ->groupBy('PIS_Goods.Code')
        ;

        $sub2 = Processor::table('PIS_Goods')
            ->select(DB::raw('PIS_Goods.Code as Code, PIS_Goods.Name as Name'))
        ;

        if (!empty($goodsCodes)) {
            $sub2->whereIn('PIS_Goods.Code', $goodsCodes);
        }

        $other = DB::table(DB::raw("({$sub->toSql()}) AS o1"))
            ->select(DB::raw(
                'o2.*, 
                o1.corpName AS corpName,
                 ISNULL(o1.total, 0) - ISNULL(o1.ReturnTotal, 0) as record, 
                 ISNULL(o1.Qty, 0) - ISNULL(o1.ReturnQty, 0) as qty'
            ))
            ->rightJoin(DB::raw("({$sub2->toSql()}) AS o2"), 'o1.Code', '=', 'o2.Code', 'full outer')
            ->orderBy('o2.code', 'ASC')
        ;

        $other->mergeBindings($sub);
        $other->mergeBindings($sub2);

        return $other;
    }
}
