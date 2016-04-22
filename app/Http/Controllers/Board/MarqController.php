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
    protected $target = [
            '客戶經營一部' => [
                '20051102' => 500000, // 許雯婷
                '20060304' => 700000, // 呂淑仙
                '20060501' => 600000, // 林淑雯
                '20060904' => 600000, // 鍾麗秀
                '20060903' => 550000, // 鍾麗秀
                '20071203' => 580000, // 湯惟喬
                '20090602' => 500000, // 邱淑伶
                '20110602' => 570000, // 蔣淑真
                '20121003' => 380000, // 張杏如
                '20130939' => 380000, // 黃麗珍
                '20141201' => 250000, // 陳品希
                '20160303' => 100000, // 蔡斐鈞
                // 'null1' => 150000,
                // 'null2' => 150000,
                // 'null3' => 150000
            ],
            '客戶經營二部' => [
                '19990107' => 700000,//'廖心瑜',
                '20060401' => 480000,//'白珮玲',
                '20080402' => 480000,//'姜宗蕙',
                '20081103' => 480000,//'陳飛燕',
                '20090402' => 480000,//'蕭愉蓁',
                '20091104' => 480000,//'廖芳賢',
                '20110807' => 480000,//'李孟娟',
                '20120405' => 480000,//'陳旻樺',
                '20130402' => 280000,//'吳華蘭',
                '20130504' => 280000,//'李慧珊',
                '20130602' => 280000,//余沛宸
                '20131007' => 280000,//'姚淑芬',
                '20160201' => 100000//'涂雅筑'
            ],
            '客戶經營三部' => [
                '20130401' => 350000, // 吳麗雅
                '20130807' => 350000, // 林晏羽
                '20130946' => 350000, // 林佩瑩
                '20051201' => 500000, // 廖雅敏
                '20140606' => 150000, // 盧品華
                '20160202' => 100000, // 趙夢梅
                '20160203' => 100000, // 萬凱雯
                '20160302' => 80000, // 考妍儒
                '20150604' => 80000 // 劉怡君
            ]
        ];

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

            $this->extendMarqTarget($data);

            return ($offset < $count) 
                ? view('board.marq.index', ['data' => $data, 'offset' => ($offset + 5), 'size' => Input::get('size', 0)]) 
                : Redirect::to('board/marq/group?timeout=' . Input::get('timeout', 10) . '&size=' . Input::get('size', 0))
            ;
        } catch (\Exception $e) {
            return view('board.marq.index', ['data' => [], 'offset' => Input::get('offset', 0)]);
        }
    }

    public function group()
    {
        $data = Processor::getArrayResult($this->getMarqGroupQuery());

        $this->extendMarqGroupTarget($data);

        return view('board.marq.group', ['data' => $this->extendGroupSum($data)]);
    }

    public function cti()
    {
        $data = Processor::getArrayResult($this->getCtiQuery(), 'Cti');
        $data = $this->genCtiDisplayData($data);
        
        $prototype = ['日通數' => 0, '日分鐘' => 0, '月通數' => 0, '月時數' => 0];

        $groups = ['客戶經營一部' => $prototype, '客戶經營二部' => $prototype, '客戶經營三部' => $prototype, '總計' => $prototype];

        foreach ($data as $row) {
            $groups[$row['部門']]['日通數'] += $row['日通數'];
            $groups[$row['部門']]['日分鐘'] += $row['日分鐘'];
            $groups[$row['部門']]['月通數'] += $row['月通數'];
            $groups[$row['部門']]['月時數'] += $row['月時數'];

            $groups['總計']['日通數'] += $row['日通數'];
            $groups['總計']['日分鐘'] += $row['日分鐘'];
            $groups['總計']['月通數'] += $row['月通數'];
            $groups['總計']['月時數'] += $row['月時數'];
        }
        
        return view('board.marq.cti', [
            'data' => $data,
            'groups' => $groups
        ]);
    }

    protected function genCtiDisplayData(array $data)
    {
        $tmp = [];

        foreach ($data as $key => $row) {
            $row['部門'] = '';

            $agentCD = trim(str_replace(["\n", "\r"], '', $row['AgentCD']));

            foreach ($this->target as $corpName => $agents) {                
                if (array_key_exists($agentCD, $agents)) {
                    $row['部門'] = $corpName;

                    break;
                }              
            }

            if ('' !== $row['部門']) {
                $tmp[] = $row;
            }            
        }

        return $tmp;
    }

    protected function getCtiQuery()
    {
        $callDate = Carbon::now()->format('Ymd');
        $endDate = Carbon::now()->format('Y-m-d H:i:s');
        $startDate = with(new Carbon('first day of this month'))->format('Y-m-d') . ' 00:00:00';

        $query = Processor::getStorageSql('Board/Marq/cti.sql');
        return str_replace(
            ['$callDate', '$startDate', '$endDate'], 
            [$callDate, $startDate, $endDate], 
            $query
        );
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function angular()
    {
        try {
            $data = Processor::getArrayResult($this->getMarqQuery());

            $count = count($data);
            $offset = Input::get('offset', 0);
            $data = array_slice($data, $offset, 8);

            $this->extendMarqTarget($data);

            return view('board.marq.rivets', ['data' => json_encode($data), 'offset' => ($offset + 5)]);
        } catch (\Exception $e) {}
    }

    protected function extendMarqTarget(array &$data)
    {
        $tmp = [];

        foreach ($data as $row) {
            $target = array_get($this->target, $row['部門'] . '.' . $row['Code'], NULL);

            $row['目標'] = $target;

            $tmp[] = $row;
        }

        $data = $tmp;

        return $this;
    }

    protected function getOffset($count)
    {
        $offset = Input::get('offset', 0);

        return $offset = ($offset > ($count + 5)) ? 0 : $offset;
    }

    protected function extendMarqGroupTarget(array &$data)
    {
        $tmp = [];
        $row['目標'] = 0;

        foreach ($data as $row) {
            $row['目標'] = $this->getGroupTotalTarget($row['部門']);

            $tmp[] = $row;
        }

        $data = $tmp;

        return $this;
    }

    protected function getGroupTotalTarget($groupName)
    {
        $totalTarget = 0;

        $targets = array_get($this->target, $groupName, []);

        foreach ($targets as $target) {
            $totalTarget += $target;
        }

        return $totalTarget;
    }

    protected function extendGroupSum(array $data)
    {    
        $today = 0;
        $thisMonth = 0;
        $target = 0;

        foreach ($data as $row) {
            $today += array_get($row, '今日業績', 0);
            $thisMonth += array_get($row, '本月累計', 0);
            $target += array_get($row, '目標', 0);
        }

        $data[] = [
            '部門' => '總計', 
            '今日業績' => $today,
            '本月累計' => $thisMonth, 
            '目標' => $target
        ];

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
