<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Utility\Chinghwa\ExportExcel;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Mail;
use Input;

class DailySaleRecordController extends Controller
{
    const CTI_JOIN_COLUMN       = '人員代碼';
    const ERP_CORPCODE_COLUMN   = '部門代碼';
    const POS_CORPCODE_COLUMN   = '門市代號';
    const POS_NONEXIST_GROUP    = '未知門市';
    const ERP_OUTTUNNEL         = 'outTunnel';

    protected $date;

    public function index()
    {
        $this->setDate();
        
        $des = '<h4>ERP</h4><pre>' . $this->getERPQuery() . '</pre>';
        $des.= '<h4>POS</h4><pre>' . $this->getPOSQuery() . '</pre>';
        $des.= '<h4>CTI</h4><pre>' . $this->getCTIQuery() . '</pre>';

        return view('basic.simple', [
            'title' => '每日業績[to副總]', 
            'des' => $des,
            'res' => NULL
        ]);
    }

    public function process()
    {
        set_time_limit(0);
        ini_set('memory_limit', '64M');

        $this
            ->setDate()
            ->setStamp(uniqid())
        ;

        $subject = '每日業績' . $this->getDate()->format('Ymd');
        $filename = ExportExcel::DSR_FILENAME . $this->getDate()->format('Ymd');
        $filePath = __DIR__ . '/../../../../storage/excel/exports/' . $filename .  '.xlsx';

        $self = $this;

        Excel::create($filename, function($excel) use ($self) {
            $excel->sheet('總表', function($sheet) use ($self) {
                $erpData = $this->fetchErpData();
                $posData = $this->fetchPosData();
                $ctiData = $this->fetchCtiData();

                $erpGroups = $this->splitErpDataByGroup($erpData);
                $posGroups = $this->splitPosDataByGroup($posData);
                
                $i = 1;
                $borderRange = 'L';
                $totalErpGroup = [];
                $totalPosGroup = [];

                $sheet
                    ->setAutoSize(true)
                    ->setFontFamily(ExportExcel::FONT_DEFAULT)
                    ->setFontSize(12)
                    ->setColumnFormat([
                        'B' => '@',
                        'F' => '#,##0_);(#,##0)',
                        'G' => '#,##0_);(#,##0)',
                        'H' => '#,##0_);(#,##0)'
                    ])
                    ->setBorder('A1:' . $borderRange .'1', ExportExcel::BOLDER_DEFAULT)
                    ->freezeFirstRow()
                ; 

                $sheet->cells('A1:' . $borderRange . '1', function ($cells) {
                    $cells->setBackground('#000000')->setFontColor('#ffffff')->setAlignment('center');
                });

                $sheet->row(1, $self->getExcelHead());

                foreach ($erpGroups as $groupCode => $group) {
                    if (self::ERP_OUTTUNNEL === $groupCode) {
                        continue;
                    }

                    foreach ($group as $agent) {
                        $display = $self->genErpDisplay($agent, $ctiData);

                        $totalErpGroup = $self->updateErpGroupTotal($totalErpGroup, $display, $groupCode);

                        $sheet->row(++ $i, $display);
                        $sheet->cells("A{$i}:{$borderRange}{$i}", function ($cells) {
                            $cells->setBackground('#827F7F')->setFontColor('#ffffff');
                        });
                    }

                    $sheet->row(++ $i, $totalErpGroup[$groupCode]);
                    $sheet->cells("A{$i}:{$borderRange}{$i}", function ($cells) {
                        $cells->setBackground('#CC0606')->setFontColor('#ffffff');
                    });
                    
                    $i ++;
                }

                $sheet->row($i, $self->getBundleTotal($totalErpGroup, '直效合計'));
                $sheet->cells("A{$i}:{$borderRange}{$i}", function ($cells) {
                    $cells->setBackground('#D48005')->setFontColor('#ffffff');
                });

                $i ++;
                if (array_key_exists(self::ERP_OUTTUNNEL, $erpGroups)) {
                    foreach ($erpGroups[self::ERP_OUTTUNNEL] as $agent) {
                        $display = $self->genErpDisplay($agent, $ctiData);

                        $totalErpGroup = $self->updateErpGroupTotal($totalErpGroup, $display, self::ERP_OUTTUNNEL);

                        $sheet->row(++ $i, $display);
                        $sheet->cells("A{$i}:{$borderRange}{$i}", function ($cells) {
                            $cells->setBackground('#827F7F')->setFontColor('#ffffff');
                        });
                    }

                    $sheet->row(++ $i, $totalErpGroup[self::ERP_OUTTUNNEL]);
                    $sheet->cells("A{$i}:{$borderRange}{$i}", function ($cells) {
                        $cells->setBackground('#CC0606')->setFontColor('#ffffff');
                    });

                    $i ++;
                }

                foreach ($posGroups as $groupCode => $group) {
                    foreach ($group as $agent) {                       
                        $display = $self->genPosDisplay($agent);
                        $sheet->row(++ $i, $display);
                        $sheet->cells("A{$i}:{$borderRange}{$i}", function ($cells) {
                            $cells->setBackground('#827F7F')->setFontColor('#ffffff');
                        });

                        $totalPosGroup = $self->updatePosGroupTotal($totalPosGroup, $display, $groupCode);
                    }

                    $sheet->row(++ $i, $totalPosGroup[$groupCode]);
                    $sheet->cells("A{$i}:{$borderRange}{$i}", function ($cells) {
                        $cells->setBackground('#CC0606')->setFontColor('#ffffff');
                    });

                    $i ++;
                }

                $sheet->row($i, $self->getBundleTotal($totalPosGroup, '直營門市合計'));
                $sheet->cells("A{$i}:{$borderRange}{$i}", function ($cells) {
                    $cells->setBackground('#D48005')->setFontColor('#ffffff');
                });

                $sheet->row(++ $i, $self->getBundleTotal(array_merge($totalErpGroup, $totalPosGroup), '公司合計'));
                $sheet->cells("A{$i}:{$borderRange}{$i}", function ($cells) {
                    $cells->setBackground('#088A1E')->setFontColor('#ffffff');
                });
           });
        })->store('xlsx', storage_path('excel/exports'));        

        Mail::send('emails.dears', ['title' => $subject], function ($m) use ($subject, $filePath) {
            $m
                ->to('sl@chinghwa.com.tw', '6700莊淑玲')
                ->cc('tonyvanhsu@chinghwa.com.tw', '6820徐士弘')
                ->cc('jocoonopa@chinghwa.com.tw', '小閎')
                ->subject($subject)
                ->attach($filePath);
            ;
        });

        return  '每日業績 send complete!';
    }

    protected function getBundleTotal(array $totalGroup, $bundleName)
    {
        $allGroup = [];
        $allGroup['部門'] = $bundleName;
        $allGroup['人員代碼'] = '';
        $allGroup['姓名'] = '';
        $allGroup['會員數'] = 0; 
        $allGroup['訂單數'] = 0; 
        $allGroup['淨額'] = 0; 
        $allGroup['會員均單'] = 0;
        $allGroup['訂單均價'] = 0;
        $allGroup['撥打會員數'] = 0;
        $allGroup['撥打通數'] = 0;
        $allGroup['撥打秒數'] = 0;
        $allGroup['工作日'] = 0; 

        foreach ($totalGroup as $groupCode => $group) {
            $allGroup['會員數'] += (int) $this->getArrayVal($group, '會員數', 0);
            $allGroup['訂單數'] += (int) $this->getArrayVal($group, '訂單數', 0);
            $allGroup['淨額'] += (int) $this->getArrayVal($group, '淨額', 0);
            $allGroup['撥打會員數'] += (int) $this->getArrayVal($group, '撥打會員數', 0);
            $allGroup['撥打通數'] += (int) $this->getArrayVal($group, '撥打通數', 0);
            $allGroup['撥打秒數'] += (int) $this->getArrayVal($group, '撥打秒數', 0);
            $allGroup['工作日'] += (int) $this->getArrayVal($group, '工作日', 0);
        }

        $allGroup['會員均單'] = (int) ($allGroup['淨額']/$allGroup['會員數']);
        $allGroup['訂單均價'] = (int) ($allGroup['淨額']/$allGroup['訂單數']);

        return $allGroup;
    }

    protected function updateErpGroupTotal(array $totalGroup, array $display, $groupCode)
    {
        if (!array_key_exists($groupCode, $totalGroup)) {
            $totalGroup[$groupCode]['部門'] = ((self::ERP_OUTTUNNEL === $groupCode) ? '外部通路' : $display['部門']). '合計';
            $totalGroup[$groupCode] = array_merge($totalGroup[$groupCode], $this->initTotalGroup());
        }

        $totalGroup[$groupCode]['會員數'] += (int) $this->getArrayVal($display, '會員數', 0);
        $totalGroup[$groupCode]['訂單數'] += (int) $this->getArrayVal($display, '訂單數', 0);
        $totalGroup[$groupCode]['淨額'] += (int) $this->getArrayVal($display, '淨額', 0);
        $totalGroup[$groupCode]['會員均單'] = (int) $totalGroup[$groupCode]['淨額']/$totalGroup[$groupCode]['會員數'];
        $totalGroup[$groupCode]['訂單均價'] = (int) $totalGroup[$groupCode]['淨額']/$totalGroup[$groupCode]['訂單數'];
        $totalGroup[$groupCode]['撥打會員數'] += (int) $this->getArrayVal($display, '撥打會員數', 0);
        $totalGroup[$groupCode]['撥打通數'] += (int) $this->getArrayVal($display, '撥打通數', 0);
        $totalGroup[$groupCode]['撥打秒數'] += (int) $this->getArrayVal($display, '撥打秒數', 0);
        $totalGroup[$groupCode]['工作日'] += (int) $this->getArrayVal($display, '工作日', 0);

        return $totalGroup;
    }

    protected function updatePosGroupTotal(array $totalGroup, array $display, $groupCode)
    {
        if (!array_key_exists($groupCode, $totalGroup)) {
            $totalGroup[$groupCode]['部門'] = $display['部門'];
            $totalGroup[$groupCode] = array_merge($totalGroup[$groupCode], $this->initTotalGroup());
        }

        $totalGroup[$groupCode]['會員數'] += (int) $this->getArrayVal($display, '會員數', 0);
        $totalGroup[$groupCode]['訂單數'] += (int) $this->getArrayVal($display, '訂單數', 0);
        $totalGroup[$groupCode]['淨額'] += (int) $this->getArrayVal($display, '淨額', 0);
        $totalGroup[$groupCode]['會員均單'] = (int) $totalGroup[$groupCode]['淨額']/$totalGroup[$groupCode]['會員數'];
        $totalGroup[$groupCode]['訂單均價'] = (int) $totalGroup[$groupCode]['淨額']/$totalGroup[$groupCode]['訂單數'];
        $totalGroup[$groupCode]['撥打會員數'] += (int) $this->getArrayVal($display, '撥打會員數', 0);
        $totalGroup[$groupCode]['撥打通數'] += (int) $this->getArrayVal($display, '撥打通數', 0);
        $totalGroup[$groupCode]['撥打秒數'] += (int) $this->getArrayVal($display, '撥打秒數', 0);
        $totalGroup[$groupCode]['工作日'] += (int) $this->getArrayVal($display, '工作日', 0);

        return $totalGroup;
    }

    protected function initTotalGroup()
    {
        $totalGroup = [];

        $totalGroup['人員代碼'] = ''; 
        $totalGroup['姓名'] = '';
        $totalGroup['會員數'] = 0;
        $totalGroup['訂單數'] = 0;
        $totalGroup['淨額'] = 0;
        $totalGroup['會員均單'] = 0;
        $totalGroup['訂單均價'] = 0;
        $totalGroup['撥打會員數'] = 0;
        $totalGroup['撥打通數'] = 0;
        $totalGroup['撥打秒數'] = 0;
        $totalGroup['工作日'] = 0; 

        return $totalGroup;
    }

    protected function genErpDisplay(array $agent, array $ctiData)
    {
        $cti = $this->getCtiFromErp($ctiData, $agent[self::CTI_JOIN_COLUMN]);

        return [
            '部門' => $agent['部門'],
            '人員代碼' => $agent[self::CTI_JOIN_COLUMN],
            '姓名' => $agent['姓名'],      
            '會員數' => $agent['會員數'],     
            '訂單數' => $agent['訂單數'],     
            '淨額' => $agent['淨額'],      
            '會員均單' => (int) $agent['淨額']/$agent['會員數'],    
            '訂單均價' => (int) $agent['淨額']/$agent['訂單數'],  
            '撥打會員數' => $cti['撥打會員數'],   
            '撥打通數' => $cti['撥打通數'], 
            '撥打秒數' => $cti['撥打秒數'], 
            '工作日' => $cti['工作日']
        ];
    }

    protected function genPosDisplay(array $agent)
    {
        return [
            '部門' => $agent['門市名稱'],
            '人員代碼' => $agent['營業員代號'],
            '姓名' => $agent['營業員名稱'],      
            '會員數' => $agent['會員數'],     
            '訂單數' => $agent['訂單數'],     
            '淨額' => $agent['金額'],      
            '會員均單' => (int) $agent['金額']/$agent['會員數'],    
            '訂單均價' => (int) $agent['金額']/$agent['訂單數'],  
            '撥打會員數' => '',   
            '撥打通數' => '', 
            '撥打秒數' => '', 
            '工作日' => ''
        ];
    }

    protected function splitErpDataByGroup(array $erpData)
    {
        $groupIndexData = [];

        $list = $this->getErpGroupList();

        foreach ($erpData as $erp) {
            $groupName = in_array($erp[self::ERP_CORPCODE_COLUMN], $list)
                ? $erp[self::ERP_CORPCODE_COLUMN] 
                : self::ERP_OUTTUNNEL;
            
            $groupIndexData[$groupName][] = $erp;
        }

        return $groupIndexData;
    }    

    protected function getErpGroupList()
    {
        return [
            'CH51000', // 直效行銷處級辦公室
            'CH53000', // 客經1
            'CH54000', // 客經2
            'CH54100', // 客經3
            'CH54200' // 客經4
        ];
    }

    protected function splitPosDataByGroup(array $posData)
    {
        $groupIndexData = [];

        $list = $this->getPosGroupList();

        foreach ($posData as $pos) {
            $groupName = (in_array($pos[self::POS_CORPCODE_COLUMN], $list)) 
                ? $pos[self::POS_CORPCODE_COLUMN] : self::POS_NONEXIST_GROUP;

            $groupIndexData[$groupName][] = $pos;
        }

        return $groupIndexData;
    }

    protected function getPosGroupList()
    {
        return [
            'S008',
            'S009',
            'S013',
            'S014',
            'S017',
            'S028',
            'S049',
            'S051'
        ];
    }

    protected function getCtiFromErp(array $ctiData, $code)
    {       
        $key = array_search(trim($code), array_column($ctiData, self::CTI_JOIN_COLUMN));

        return (false !== $key) ? $ctiData[$key] : null;
    }

    protected function fetchPosData()
    {
        if (!($res = odbc_exec($this->connectToPos(), $this->cb5($this->getPOSQuery())))) {
            return null;
        }
        
        return $this->transResToArr($res);
    }

    protected function fetchCtiData()
    {
        if (!($res = odbc_exec($this->connectToCti(), $this->cb5($this->getCTIQuery())))) {
            return null;
        }
        
        return $this->transResToArr($res);
    }

    protected function fetchErpData()
    {
        if (!($res = odbc_exec($this->connectToErp(), $this->cb5($this->getERPQuery())))) {
            return null;
        }
        
        return $this->transResToArr($res);
    }

    protected function transResToArr($res) 
    {
        $data = [];

        while ($row = odbc_fetch_array($res)) {
            $this->c8res($row);

            $data[] = $row;
        }

        return $data;
    }

    protected function getCustomCarry_1_Codes()
    {
        return [
            '20051102',
            '20060304',
            '20060501',
            '20060903',
            '20060904',
            '20071203',
            '20090602',
            '20110602',
            '20130401',
            '20131006',
            '20140705',
            '20141201',
            '20150707'
        ];
    }

    protected function getCustomCarry_2_Codes()
    {
        return [
            '19990107',
            '20051201',
            '20060401',
            '20080402',
            '20081103',
            '20090402',
            '20091104',
            '20110807',
            '20120405',
            '20130402',
            '20130403',
            '20130504',
            '20130601',
            '20130602',
            '20131005'
        ];
    }

    protected function getCustomCarry_3_Codes()
    {
        return [
            '20100308',
            '20100310',
            '20100607',
            '20120605',
            '20120703',
            '20150504',
            '20150606',
            '20150905'
        ];
    }

    protected function getCustomCarry_4_Codes()
    {
        return [
            '20090504',
            '20121003',
            '20130807',
            '20130939',
            '20130946',
            '20131007',
            '20140303',
            '20140606'
        ];
    }

    protected function getMarketingCodes()
    {
        return ['20091205'];
    }

    protected function getOutTunnelCodes()
    {
        return [
            '20090558',
            '2724031300',
            '1660610201',
            '2736592500',
            '9692235500',
            '20090516',
            '0379980200',
            '2845204700',
            '8444276900',
            '1608079500',
            '9717627000',
            '7046107500',
            '20100904',
            '20101101',
            '20110704',
            '5367210400',
            '2431701400'
        ];
    }

    protected function getS009Codes()
    {
        return [
            'S009-279',
            'S009-291'
        ];
    }

    protected function getS013Codes()
    {
        return [
            'S013-028',
            'S013-294',
            'S013-296',
            'S013-297'
        ];
    }

    protected function getS049Codes()
    {
        return [
            'S049-028',
            'S049-292',
            'S049-296',
            'S049-297'
        ];
    }

    protected function getS008Codes()
    {
        return [
            'S008-036',
            'S008-126'
        ];
    }

    protected function getS014Codes()
    {
        return [
            'S014-134',
            'S014-212'
        ];
    }

    protected function getS017Codes()
    {
        return [
            'S017-116',
            'S017-139'
        ];
    }

    protected function getS028Codes()
    {
        return [
            'S028-097',
            'S028-281'
        ];
    }

    protected function getS051Codes()
    {
        return [
            'S051-034',
            'S051-058'
        ];
    }

    protected function setDate()
    {
        $this->date = new \DateTime();
        $this->date->modify('-1 Days');

        return $this;
    }

    protected function getDate()
    {
        return $this->date;
    }

    protected function setStamp($stamp = NULL)
    {
        $this->stamp = $stamp;

        return $this;
    }

    protected function getStamp()
    {
        return $this->stamp;
    }

    protected function getStampFileName()
    {
        return ExportExcel::DSR_FILENAME . $this->getStamp();
    }

    protected function getERPQuery()
    {
        return str_replace(
            ['$startDate', '$endDate'],
            [$this->getDate()->modify('first day of this month')->format('Ymd'), $this->getDate()->modify('last day of this month')->format('Ymd')],
            file_get_contents(__DIR__ . '/../../../../storage/sql/DailySaleRecord/ERP.sql')
        );
    }

    protected function getPOSQuery()
    {
        return str_replace(
            ['$startDate', '$endDate'],
            [$this->getDate()->modify('first day of this month')->format('Ymd'), $this->getDate()->modify('last day of this month')->format('Ymd')],
            file_get_contents(__DIR__ . '/../../../../storage/sql/DailySaleRecord/POS.sql')
        );
    }

    protected function getCTIQuery()
    {
        return str_replace(
            ['$startDate', '$endDate'],
            [$this->getDate()->modify('first day of this month')->format('Ymd'), $this->getDate()->modify('last day of this month')->format('Ymd')],
            file_get_contents(__DIR__ . '/../../../../storage/sql/DailySaleRecord/CTI.sql')
        );
    }

    protected function getFilePath()
    {
        return $_SERVER['DOCUMENT_ROOT'] . '/../storage/excel/import/' . $this->getStampFileName() . 'xlsx';
    }

    protected function getExcelHead()
    {
        return [
            '部門',
            '人員代碼',
            '姓名',      
            '會員數',     
            '訂單數',     
            '淨額',      
            '會員均單',    
            '訂單均價',    
            '撥打會員數',   
            '撥打通數',    
            '撥打秒數',
            '工作日' 
        ];
    }
}