<?php

namespace App\Http\Controllers\Test;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Utility\Chinghwa\Database\Query\Processors\Processor;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function backmail() 
    {
        set_time_limit(0);

        $startTime = microtime(true);

        $chunkSize = 200;
        $r = explode (',', file_get_contents(__DIR__ . '/backemail.txt'));

        $realGetCountTotal = 0;
        $totalNum = count($r);

        for ($i = 0; $i < $totalNum; $i = $i + $chunkSize) {
            $partialArr = array_slice($r, $i, $chunkSize);
            
            $data = Processor::getArrayResult($this->getQuery($partialArr));
            $realGetCount = count($data);

            echo "{$i}:realGet:{$realGetCount}<br/>";
            Processor::execErp($this->getUpdateQuery(array_pluck($data, 'SerNo')));

            $realGetCountTotal += $realGetCount;
        }

        $endTime = microtime(true);

        dd("費時:" . floor($endTime - $startTime) . ",共計{$realGetCountTotal}人");
    }

    protected function getQuery($partialArr) 
    {
        return "SELECT POS_Member.SerNo, POS_Member.Code, POS_Member.Name, POS_Member.E_Mail, CCS_MemberFlags.Distflags_6  FROM POS_Member WITH(NOLOCK) LEFT JOIN CCS_MemberFlags WITH(NOLOCK) ON CCS_MemberFlags.MemberSerNoStr=POS_Member.SerNo WHERE POS_Member.E_Mail IN(" . implode(',', $partialArr) . ")";
    }

    protected function getUpdateQuery($sernos)
    {
        return "UPDATE CCS_MemberFlags SET Distflags_6='A' WHERE CCS_MemberFlags.MemberSerNoStr IN('" . implode("','", $sernos) . "')";
    }
}
