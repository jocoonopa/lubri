<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Utility\Chinghwa\ExportExcel;
use App\Utility\Chinghwa\Helper\Excel\ExcelHelper;
use App\Utility\Chinghwa\Database\Query\Processors\Processor;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Mail;
use Input;

class RetailSalesController extends Controller
{
	public $colorIndex = [];
    protected $dateObj;

    public function index()
    {
        $date = new \DateTime;
        $date->modify('-1 Days');

        return view('basic.simple', [
            'title' => '門市營業額分析日報表', 
            'des' => '<h4>每日寄送</h4><pre>' . $this->getQuery($date) . '</pre>',
            'res' => ''
        ]);
    }

    public function process()
    {   
    	$self = $this;
    	$container = [];
    	$config = $this->getConfig();
        $this->setDateObj(new \DateTime);
        $date = $this->getDateObj();

    	// 取得資料
    	if ($res = Processor::execPos($self->getQuery($date))) {
            while ($data = odbc_fetch_array($res)) {
            	$tmp = [];
                foreach ($data as $key => $val) {
                    $tmp[c8($key)] = (string) c8($val);
                }

                $tmp['goal'] = $config[$tmp['STOCK_NO']]['goal'];
                $tmp['pl'] = $config[$tmp['STOCK_NO']]['pl'];

                $container[$tmp['STOCK_NO']] = $tmp;
                
                unset($data);
            }
        }

        foreach ($this->getNorthGroup() as $val) {
    		$this->pushInReindex($container, $val);
    	}

    	foreach ($this->getSorthGroup() as $val) {
    		$this->pushInReindex($container, $val);
    	}

        $container['north'] = $this->genNorthData($container);
        $container['sorth'] = $this->genSorthData($container);
        $container['total'] = $this->genTotalData($container);
        $container['total']['STOCK_NO'] = '總計';

        $this->reindex($container);

        // 生成Excel檔案
        $this->genFile($container)->store('xls', storage_path('excel/exports'));

        $filePath = __DIR__ . '/../../../../storage/excel/exports/' . ExportExcel::RS_FILENAME . '_' . $this->getDateObj()->format('Ymd') . '.xls';
        $subject = '門市營業額分析日報表' . $this->getDateObj()->format('Ym') . '01-'. $this->getDateObj()->format('d');
        $self = $this;

        Mail::send('emails.creditCard', ['title' => $subject], function ($m) use ($subject, $filePath, $self) {
            $m->subject($subject)->to($self->getToList())->cc($self->getCCList())->attach($filePath);
        });

        return '門市營業額分析日報表 Send Complete!';
    }

    protected function getToList()
    {
        return [
            'lingying3025@chinghwa.com.tw' => '6521吳俐穎'
        ];
    }

    protected function getCCList()
    {
        return [
            'meganlee@chinghwa.com.tw' => '6500李惠淑',
            'sl@chinghwa.com.tw' => '6700莊淑玲',
            'swhsu@chinghwa.com.tw' => '6800徐士偉',
            'leaha@chinghwa.com.tw' => '6150陳佳莉',
            'tonyvanhsu@chinghwa.com.tw' => '6820徐士弘',
            's008@chinghwa.com.tw' => 'S008高雄SOGO門市',
            's009@chinghwa.com.tw' => 'S009美麗華門市',
            's013@chinghwa.com.tw' => 'S013新光站前',
            's014@chinghwa.com.tw' => 'S014新光台中',
            's017@chinghwa.com.tw' => 'S017大統百貨',
            's028@chinghwa.com.tw' => 'S028台南西門新光百貨',
            's049@chinghwa.com.tw' => 'S049新光A8',
            's051@chinghwa.com.tw' => 'S051漢神小巨蛋',
            'jocoonopa@chinghwa.com.tw' => '6231小閎'
        ];
    }

    protected function getTotalGroup()
    {
    	return ['S008', 'S014', 'S017', 'S028', 'S051', 'S009', 'S013', 'S049'];
    }

    protected function getSorthGroup()
    {
    	return ['S008', 'S014', 'S017', 'S028', 'S051'];
    }

    protected function getNorthGroup()
    {
    	return ['S009', 'S013', 'S049'];
    }

    protected function genMockStore(&$container, $val)
    {
    	reset($container);
		$firstKey = key($container);

    	$container[$val] = $container[$firstKey];
    	$config = $this->getConfig();

		foreach ($container[$val] as $key => $ele) {
			$container[$val][$key] = 0;
		}

		$container[$val]['STOCK_NO'] = $val;
		$container[$val]['分區'] = in_array($val, $this->getSorthGroup()) ? '南區' : '北區';
		$container[$val]['pl'] = $config[$val]['pl'];
		$container[$val]['goal'] = $config[$val]['goal'];

		return $this;
    }

    protected function pushInReindex(array &$container, $val)
    {
    	if (!array_key_exists($val, $container)) {
    		$this->genMockStore($container, $val);
    	}

    	return $this;
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
    		$arr[] = $container[$val];
    	}

    	$arr[] = $container['total'];
    	$this->colorIndex[] = count($arr) + 1;

    	$container = $arr;

    	return $this;
    }

    protected function groupAreaData(array $container, array $arr)
    {
    	$area = [];
    	$stores = $arr;

    	foreach ($container[$arr[0]] as $key => $val) {
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
    	$area['STOCK_NO'] = $container[$arr[0]]['分區'];

    	return $area;
    } 

    protected function genTotalData(array $container)
    {
    	return $this->groupAreaData($container, $this->gettotalGroup());
    }

    protected function genSorthData(array $container)
    {
    	return $this->groupAreaData($container, $this->getSorthGroup());
    }

    protected function genNorthData(array $container)
    {
    	return $this->groupAreaData($container, $this->getNorthGroup());
    }

    protected function genFile($container)
    {
    	$self = $this;

        return Excel::create(ExportExcel::RS_FILENAME . '_' . $this->getDateObj()->format('Ymd'), function($excel) use ($self, &$container) {
            $excel->setTitle(ExportExcel::RS_TITLE);

            $excel->setCreator('mis@chinghwa.com.tw')
                    ->setCompany('chinghwa');

            $excel->sheet('總表', function($sheet) use ($self, &$container) {
                $sheet
                    ->setAutoSize(true)
                    ->setFontFamily('細明體')
                    ->setFontSize(10)
                    ->row(1, []) // 留白
                    ->row(2, []) // 留白
                    ->row(3, $self->getSpecificNav())
                    ->row(4, $self->getHeadRow())
                    ->setColumnFormat(array(
                        'E' => '0%',
                        'G' => '0%',
                        'L' => '0%',
                        'N' => '0%',
                        'R' => '0%'
                    ))
                ; 

                $sheet->cells('A1:Y1', function ($cells) {
                    $cells->setBorder('none');
                });

                $rows = array();
                $config = $this->getConfig();

                foreach ($container as $store) {
                    $row[ExcelHelper::rmi('A')] = array_key_exists($store['STOCK_NO'], $config)
                        ? $store['STOCK_NO'] . $config[$store['STOCK_NO']]['name']
                        : $store['STOCK_NO'];
                    $row[ExcelHelper::rmi('B')] = @number_format($store['goal']);
                    $row[ExcelHelper::rmi('C')] = @number_format($store['goal']);
                    $row[ExcelHelper::rmi('D')] = @number_format($store['累計實績']);
                    $row[ExcelHelper::rmi('E')] = $store['累計實績']/$store['goal'];
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

                foreach ($this->colorIndex as $index) {
                	$sheet->cells('B' . ($index + 3) . ':Y' . ($index + 3), function ($cells) use ($sheet) {
	                    $cells->setFontSize(11);
	                });

	                $sheet->cells('A' . ($index + 3) . ':A' . ($index + 3), function ($cells) {
	                	$cells->setFontWeight('bold');
	                });
                }

                $startRow = 3;
                $lastRow = ($startRow + count($container) + 1);

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
            });
        });
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

    protected function getQuery(\DateTime $date)
    {
        // Prevent reference link error
        $cloneDate = new \DateTime($date->format('Y-m-d H:i:s'));

    	$pszCurrentYear = $cloneDate->format('Y');
    	$pszCurrentMonth = $cloneDate->format('m');
		$pszCurrentDay = $cloneDate->format('d');
		$pszPastYear = $cloneDate->modify('-1 year')->format('Y');
		$pszTailDate = $pszCurrentMonth . $pszCurrentDay;
		$pszPastYearLastDayThisMonth = $cloneDate->modify('last day of this month')->format('d');

    	return str_replace(
            ['$pszCurrentYear', '$pszCurrentMonth', '$pszCurrentDay', '$pszPastYearLastDayThisMonth', '$pszPastYear', '$pszTailDate'],
            [$pszCurrentYear, $pszCurrentMonth, $pszCurrentDay, $pszPastYearLastDayThisMonth, $pszPastYear, $pszTailDate],
            file_get_contents(__DIR__ . '/../../../../storage/sql/RetailSales.sql')
        );
    }

    /**
     * 取得目前月份的目標(201601)
     * 
     * @return array
     */
    protected function getConfig()
    {
        return [
            'S009' => [
                'name' => '大直門市部',
                'goal' => 280000,
                'pl' => 0 
            ],
            'S013' => [
                'name' => '新光站前',
                'goal' => 550000,
                'pl' => 0 
            ],
            'S049' => [
                'name' => '新光A8館',
                'goal' => 520000,
                'pl' => 0 
            ],
            'S008' => [
                'name' => '高雄SOGO門市部',
                'goal' => 200000,
                'pl' => 0 
            ],
            'S014' => [
                'name' => '新光台中',
                'goal' => 710000,
                'pl' => 0 
            ],
            'S017' => [
                'name' => '大統百貨',
                'goal' => 300000,
                'pl' => 0 
            ],
            'S028' => [
                'name' => '台南新天地',
                'goal' => 505000,
                'pl' => 0 
            ],
            'S051' => [
                'name' => '漢神巨蛋',
                'goal' => 410000,
                'pl' => 0
            ]
        ];
    }

    protected function setDateObj(\DateTime $obj)
    {
        $obj->modify('-1 Days');
        $this->dateObj = $obj;

        return $this;
    }

    protected function getDateObj()
    {
        return $this->dateObj;
    }
}