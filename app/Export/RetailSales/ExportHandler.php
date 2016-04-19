<?php

namespace App\Export\RetailSales;

use App\Model\Pos\Store\Store;
use App\Model\Pos\Store\StoreArea;
use App\Model\Pos\Store\StoreGoal;
use App\Utility\Chinghwa\Database\Query\Processors\Processor;
use App\Utility\Chinghwa\Helper\Excel\ExcelHelper;
use Carbon\Carbon;
use Input;

class ExportHandler implements \Maatwebsite\Excel\Files\ExportHandler
{
    protected $colorIndex = [];
    protected $dateObj;

    public function handle($export)
    {
        return $this->process($export);
    }

    public function process(Export $export)
    {
        $container = [];
        
        $this->setDateObj($export->getDate());
        $config = $this->getConfig($export);

        // 取得資料
        $this->fillGoalAndPlInContainer($container, $config);
        $this->refactInGroupType($export, $container, $this->getNorthGroup());
        $this->refactInGroupType($export, $container, $this->getSorthGroup());
        $this->fillSummaryAttr($container);
        $this->reindex($container);

        // 生成Excel檔案
        if (!$export->getIsExport()) {
            $this->genFile($export, $container)->store('xls', storage_path('excel/exports'));
        }        

        return $export;
    }

    protected function refactInGroupType($export, &$container, $groups)
    {
        foreach ($groups as $sn) {
            $this->pushInReindex($export, $container, $sn);            
        }

        return $this;
    }

    protected function pushInReindex($export, array &$container, $sn)
    {
        if (!array_key_exists($sn, $container)) {
            $this->genMockStore($export, $container, $sn);
        }

        return $this;
    }

    protected function genMockStore($export, &$container, $val)
    {
        reset($container);

        $_sn = key($container);

        $container[$val] = $container[$_sn];
        $config = $this->getConfig($export);

        foreach ($container[$val] as $key => $ele) {
            $container[$val][$key] = 0;
        }

        $container[$val]['STOCK_NO'] = $val;
        $container[$val]['分區'] = in_array($val, $this->getSorthGroup()) ? '南區' : '北區';
        $container[$val]['pl'] = $config[$val]['pl'];
        $container[$val]['goal'] = $config[$val]['goal'];

        return $this;
    }

    protected function fillSummaryAttr(&$container)
    {
        $container['north'] = $this->genNorthData($container);
        $container['sorth'] = $this->genSorthData($container);
        $container['total'] = $this->genTotalData($container);

        $container['total']['STOCK_NO'] = '總計';
        $container['north']['STOCK_NO'] = '北區';
        $container['sorth']['STOCK_NO'] = '南區';

        return $this;
    }

    protected function fillGoalAndPlInContainer(&$container, $config)
    {
        // 取得資料
        if ($res = Processor::execPos($this->getQuery($this->getDateObj()))) {
            while ($data = odbc_fetch_array($res)) {
                $tmp = [];
                foreach ($data as $key => $val) {
                    $tmp[c8($key)] = (string) c8($val);
                }

                $tmp['goal'] = $config[trim($tmp['STOCK_NO'])]['goal'];
                $tmp['pl'] = $config[trim($tmp['STOCK_NO'])]['pl'];

                $container[$tmp['STOCK_NO']] = $tmp;

                unset($data);
            }
        }

        return $this;
    }

    protected function getTotalGroup()
    {
        return $this->pluckSnFromStores(Store::findActive()->orderBy('sn')->get());
    }

    protected function getSorthGroup()
    {
        return $this->pluckSnFromStores(Store::findActive()->where('store_area_id', '=', 2)->get());
    }

    protected function getNorthGroup()
    {
        return $this->pluckSnFromStores(Store::findActive()->where('store_area_id', '=', 1)->get());
    }

    protected function pluckSnFromStores($stores) 
    {
        $group = [];

        $stores->each(function ($store) use (&$group) {
            $group[] = $store->sn;
        });

        return $group;
    }

    /**
     * 取得目前月份的目標(201601)
     * 
     * @return array
     */
    protected function getConfig($export)
    {
        $goalWithSnKey = [];

        $goals = StoreGoal::with(['store'])
            ->findByYear($export->getDate()->format('Y'))
            ->findByMonth($export->getDate()->format('m'))
            ->get()
        ;

        foreach ($goals as $goal) {
            $goalWithSnKey[$goal->store->sn] = [
                'name' => $goal->store->name,
                'goal' => $goal->origin_goal,
                'pl' => $goal->pl_origin_goal
            ];
        }        

        return $goalWithSnKey;
    }

    protected function reindex(array &$container)
    {
        $arr = [];

        $arr[] = $container['north'];
        $this->colorIndex[] = count($arr) + 1;

        foreach ($this->getNorthGroup() as $val) {
            $arr[] = $container[$val];
        }

        $arr[] = $container['sorth'];
        $this->colorIndex[] = count($arr) + 1;

        foreach ($this->getSorthGroup() as $val) {
            $arr[] = array_get($container, $val, []);
        }

        $arr[] = $container['total'];
        $this->colorIndex[] = count($arr) + 1;

        $container = $arr;

        return $this;
    }

    

    protected function genTotalData(array $container)
    {
        return $this->groupAreaData($container, $this->getTotalGroup());
    }

    protected function genSorthData(array $container)
    {
        return $this->groupAreaData($container, $this->getSorthGroup());
    }

    protected function genNorthData(array $container)
    {
        return $this->groupAreaData($container, $this->getNorthGroup());
    }

    /**
     * groupAreaData description
     * 
     * @param  array  $container 
     * @param  array  $arr       區的店代碼陣列
     * @return array  $area
     */
    protected function groupAreaData(array $container, array $arr)
    {
        $area = [];
        $stores = $arr;

        foreach (array_get($container, $arr[0], []) as $key => $val) {
            if ('分區' === $key) {
                continue;   
            }

            $area[$key] = 0;

            foreach ($stores as $sn) {
                if (!array_key_exists($sn, $container)) {
                    continue;
                }

                $area[$key] += $container[$sn][$key];
            }
        }

        $area['分區'] = '';
        $area['STOCK_NO'] = array_get($container, $arr[0] . '分區');

        return $area;
    } 

    protected function genFile($export, $container)
    {
        return $export->sheet('總表', $this->sheetCallBack($export, $container));
    }

    protected function setBasicStyle($sheet)
    {
        $sheet
            ->setAutoSize(true)
            ->setFontFamily('細明體')
            ->setFontSize(10)
            ->row(1, []) // 留白
            ->row(2, []) // 留白
            ->row(3, $this->getSpecificNav())
            ->row(4, $this->getHeadRow())
            ->setColumnFormat(array(
                'E' => '0%',
                'G' => '0%',
                'L' => '0%',
                'N' => '0%',
                'R' => '0%'
            ))
        ;

        return $this;
    }

    protected function appendRowsByContainer($sheet, $container, $config)
    {
        $rows = [];
            
        foreach ($container as $store) {
            $row = [];

            $row[ExcelHelper::rmi('A')] = array_key_exists($store['STOCK_NO'], $config)
                ? $store['STOCK_NO'] . $config[$store['STOCK_NO']]['name']
                : $store['STOCK_NO'];
            $row[ExcelHelper::rmi('B')] = @number_format($store['goal']);
            $row[ExcelHelper::rmi('C')] = @number_format($store['goal']);
            $row[ExcelHelper::rmi('D')] = @number_format($store['累計實績']);
            $row[ExcelHelper::rmi('E')] = (0 != $store['goal']) ? $store['累計實績']/$store['goal'] : 0;
            $row[ExcelHelper::rmi('F')] = @number_format($store['去年同期']);
            $row[ExcelHelper::rmi('G')] = (0 < $store['去年同期'])? $store['累計實績']/$store['去年同期'] - 1 : 0;
            $row[ExcelHelper::rmi('H')] = @number_format($store['累計實績'] - $store['goal']);
            $row[ExcelHelper::rmi('I')] = @number_format($store['去年當月']);
            $row[ExcelHelper::rmi('J')] = @number_format($store['累計實績'] - $store['去年當月']);
            $row[ExcelHelper::rmi('K')] = @number_format($store['PL業績']);
            $row[ExcelHelper::rmi('L')] = (0 < $store['累計實績'])? $store['PL業績']/$store['累計實績'] : 0;
            $row[ExcelHelper::rmi('M')] = @number_format($store['去年同期PL']);
            $row[ExcelHelper::rmi('N')] = (0 < $store['去年同期PL'])? $store['PL業績']/$store['去年同期PL'] - 1 : 0;
            $row[ExcelHelper::rmi('O')] = @number_format($store['pl']);
            $row[ExcelHelper::rmi('P')] = @number_format($store['pl'] - $store['PL業績']);
            $row[ExcelHelper::rmi('Q')] = @number_format($store['毛利']);
            $row[ExcelHelper::rmi('R')] = (0 < $store['累計實績'])? $store['毛利']/$store['累計實績'] : 0;
            $row[ExcelHelper::rmi('S')] = @number_format($store['PL毛利']);
            $row[ExcelHelper::rmi('T')] = $store['本月來客'];
            $row[ExcelHelper::rmi('U')] = $store['去年同期來客'];
            $row[ExcelHelper::rmi('V')] = $store['本月來客'] - $store['去年同期來客'];
            $row[ExcelHelper::rmi('W')] = (0 < $store['本月來客'])? @floor($store['累計實績'] / $store['本月來客']) : 0;
            $row[ExcelHelper::rmi('X')] = (0 < $store['去年同期來客'])? floor($store['去年同期']/$store['去年同期來客']) : 0;
            $row[ExcelHelper::rmi('Y')] = (0 < $store['本月來客'])? @floor(($store['累計實績'] / $store['本月來客']) - ($store['去年同期']/$store['去年同期來客'])) : 0;

            $rows[] = $row;
        }

        $sheet->rows($rows);

        return $this;
    }

    protected function drawBorder($sheet, $container)
    {
        $startRow = $this->getStartRowAndLastRow($container)[0];
        $lastRow = $this->getStartRowAndLastRow($container)[1];

        $sheet->cells('A1:Y1', function ($cells) {
            $cells->setBorder('none');
        });

        foreach ($this->colorIndex as $index) {
            $sheet->cells('B' . ($index + 3) . ':Y' . ($index + 3), function ($cells) use ($sheet) {
                $cells->setFontSize(11);
            });

            $sheet->cells('A' . ($index + 3) . ':A' . ($index + 3), function ($cells) {
                $cells->setFontWeight('bold');
            });
        }

        // 畫框線 Begin 
        // 左側Title邊框細線設定
        $leftArr = ['B', 'I', 'K', 'Q', 'T'];

        foreach ($leftArr as $index) {
            $sheet->cells($index . '3:' . $index . '4', function ($cells) {
                $cells->setBorder('none', 'none', 'none', 'thin');
            });
        }

        // 整個 sheet 最外部的粗框
        $sheet->cells('A3:Y' . $lastRow, function ($cells) {
            $cells->setBorder('thick', 'thick', 'thick', 'thick');
        });

        // 北區->總計資料細框
        $sheet->setBorder('B5:Y' . $lastRow, 'thin');

        // 北區粗框
        $sheet->cells('A' . ($startRow + 2) . ':Y' . ($startRow + $this->colorIndex[1]), function ($cells) {
            $cells->setBorder('thick', 'thick', 'none', 'thick');
        });

        // 南區粗框
        $sheet->cells('A' . ($startRow + $this->colorIndex[1]) .':Y' . ($lastRow - 1), function ($cells) {
            $cells->setBorder('thick', 'thick', 'none', 'thick');
        });

        // 總計粗框
        $sheet->cells('A' . $lastRow .':Y' . $lastRow, function ($cells) {
            $cells->setBorder('thick', 'thin', 'thick', 'thick');
        });
        // 最後一格補粗框
        $sheet->cells('Y' . $lastRow .':Y' . $lastRow, function ($cells) {
            $cells->setBorder('thick', 'thick', 'thick', 'thin');
        });

        // 畫框線 End
         
        return $this;
    }

    protected function drawFontColor($sheet, $container)
    {
        $startRow = $this->getStartRowAndLastRow($container)[0];
        $lastRow = $this->getStartRowAndLastRow($container)[1];

        // 上色
        $colorColArr = [
            '#105C92' => ['O', 'B', 'C'],
            '#DA1E00' => ['P', 'H', 'J'],
            '#356F17' => ['Y', 'V']
        ];

        foreach ($colorColArr as $colorCode => $col) {
            foreach ($col as $index) {
                $sheet->cells($index . $startRow .':' . $index . $lastRow, function ($cells) use ($colorCode) {
                    $cells->setFontColor($colorCode);
                });
            }
        }

        return $this;
    }

    protected function getStartRowAndLastRow($container)
    {
        $startRow = 3;
        $lastRow = ($startRow + count($container) + 1);

        return [$startRow, $lastRow];
    }

    protected function sheetCallBack($export, &$container)
    {
        return function($sheet) use ($export, &$container) {
            $this
                ->setBasicStyle($sheet)
                ->appendRowsByContainer($sheet, $container, $this->getConfig($export))
                ->drawBorder($sheet, $container)
                ->drawFontColor($sheet, $container)
            ;                        
        };
    }

    protected function getSpecificNav()
    {
        $arr = array_fill(0, 25, NULL);
        $arr[ExcelHelper::rmi('B')] = '本月業績';
        $arr[ExcelHelper::rmi('I')] = '去年';
        $arr[ExcelHelper::rmi('K')] = 'PL業績(含稅)';
        $arr[ExcelHelper::rmi('Q')] = '毛利(含稅)';
        $arr[ExcelHelper::rmi('T')] = '來客&客單';

        return $arr;
    }

    protected function getHeadRow()
    {
        return [
            '    ', '預算目標', '月門市目標', '累計實績', '達成率',
            '去年同期', '同期成長', '目標差距', '去年當月業績','與去年當月差距',
            '本月PL','PL佔總業績(百分比)','去年同期',
            '同期成長','PL目標','PL差距','本月毛利','毛利率',  
            'PL毛利','本月來客','去年同期來客','來客成長',
            '本月客單','去年同期客單','客單成長'
        ];
    }

    protected function getQuery(Carbon $date)
    {
        // Prevent reference link error
        $cloneDate = new Carbon($date->format('Y-m-d H:i:s'));

        $pszCurrentYear              = $cloneDate->format('Y');
        $pszCurrentMonth             = $cloneDate->format('m');
        $pszCurrentDay               = $cloneDate->format('d');
        $pszPastYear                 = $cloneDate->subYear()->format('Y');
        $pszTailDate                 = $pszCurrentMonth . $pszCurrentDay;
        $pszPastYearLastDayThisMonth = $cloneDate->modify('last day of this month')->format('d');

        return str_replace(
            ['$pszCurrentYear', '$pszCurrentMonth', '$pszCurrentDay', '$pszPastYearLastDayThisMonth', '$pszPastYear', '$pszTailDate'],
            [$pszCurrentYear, $pszCurrentMonth, $pszCurrentDay, $pszPastYearLastDayThisMonth, $pszPastYear, $pszTailDate],
            Processor::getStorageSql('RetailSales.sql')
        );
    }

    protected function setDateObj(Carbon $obj)
    {
        $this->dateObj = $obj;

        return $this;
    }

    protected function getDateObj()
    {
        return $this->dateObj;
    }
}