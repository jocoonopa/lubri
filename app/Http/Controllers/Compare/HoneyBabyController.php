<?php

namespace App\Http\Controllers\Compare;

use Validator;
use Input;
use App\Http\Controllers\Controller;
use App\Utility\Chinghwa\ExportExcel;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class HoneyBabyController extends Controller
{
    const CHUNK_SIZE                                   = 200;
    
    const MEMBER_CATEGORY_CODE                         = '126';
    const MEMBER_LEVEL_CODE                            = '00-01';
    const MEMBER_EMP_CODE                              = '20090568';
    
    const IMPORT_NAME_INDEX                            = 1;
    const IMPORT_BIRTHDAY_INDEX                        = 2;
    const IMPORT_AREACODE_INDEX                        = 3;
    const IMPORT_STATE_INDEX                           = 4;
    const IMPORT_CITY_INDEX                            = 5;
    const IMPORT_ADDRESS_INDEX                         = 6;
    const IMPORT_HOMETEL_INDEX                         = 7;
    const IMPORT_COMPANYTEL_INDEX                      = 8;
    const IMPORT_MOBILE_INDEX                          = 9;
    const IMPORT_EMAIL_INDEX                           = 10;
    const IMPORT_FLAG23_INDEX                          = 11;
    const IMPORT_OLDMEMO_INDEX                         = 17;
    const IMPORT_NEWMEMO_INDEX                         = 14;
    
    const EXPORT_INSERT_MEMBERINFO_SERNO_INDEX         = 0;
    const EXPORT_INSERT_MEMBERINFO_CREATEDATE_INDEX    = 1;
    const EXPORT_INSERT_MEMBERINFO_NAME_INDEX          = 2;
    const EXPORT_INSERT_MEMBERINFO_SEX_INDEX           = 3;
    const EXPORT_INSERT_MEMBERINFO_BIRTHDAY_INDEX      = 4;
    const EXPORT_INSERT_MEMBERINFO_CATEGORYCODE_INDEX  = 6;
    const EXPORT_INSERT_MEMBERINFO_DISCODE_INDEX       = 7;
    const EXPORT_INSERT_MEMBERINFO_LEVEL_INDEX         = 8;
    const EXPORT_INSERT_MEMBERINFO_HOMETEL_INDEX       = 12;
    const EXPORT_INSERT_MEMBERINFO_COMPANYTEL_INDEX    = 14;
    const EXPORT_INSERT_MEMBERINFO_MOBILE_INDEX        = 16;
    const EXPORT_INSERT_MEMBERINFO_AREACODE_PREV_INDEX = 17;
    const EXPORT_INSERT_MEMBERINFO_AREACODE_NEXT_INDEX = 18;
    const EXPORT_INSERT_MEMBERINFO_ADDRESS_INDEX       = 19;
    const EXPORT_INSERT_MEMBERINFO_EMAIL_INDEX         = 23;
    const EXPORT_INSERT_MEMBERINFO_EMPCODE_INDEX       = 24;
    const EXPORT_INSERT_MEMBERINFO_MEMO_INDEX          = 40;

    const TW_DATE_LENGTH                               = 6;
    const BASE_YEAR                                    = 1911;

    protected $memberCode;
    protected $date;
    protected $stamp;

    public function index(Request $request)
    {
        return view('compare.honeybaby.index', ['title' => '寵兒比對', 'res' => '']);
    }

    public function process(Request $request)
    {   
        set_time_limit(0);
        ini_set('memory_limit', '512M');

        $validator = Validator::make($request->all(),  ['excel' => 'required|mimes:xlsx']);

        if ($validator->fails()) {
            return view('compare.honeybaby.index', [
                'title' => '寵兒比對', 
                'res' => '檔案驗證不合法']
            );
        }

        $startTime = microtime(true);

        $this
            ->setDate()
            ->setStamp(uniqid())
            ->genFlapInsertFile()
            ->genFlapUpdateFile()
        ; 
        
        $data = $this->loadSourceFile($this->moveUploadFile());

        $endTime = microtime(true);

        return view('compare.honeybaby.index', [
            'title' => '寵兒比對', 
            'res' => view('compare.honeybaby.test', [
                'exectime' => floor($endTime - $startTime),
                'stamp' => $this->getStamp()
            ]),
        ]);
    }

    public function downloadInsert(Request $request)
    {
        return Excel::load($_SERVER['DOCUMENT_ROOT'] . '/../storage/excel/exports/' .  ExportExcel::HONEYBABY_FILENAME . Input::get('stamp') . '_Insert.xls')
        ->export();
    }

    public function downloadUpdate(Request $request)
    {
        return Excel::load($_SERVER['DOCUMENT_ROOT'] . '/../storage/excel/exports/' .  ExportExcel::HONEYBABY_FILENAME . Input::get('stamp') . '_Update.xls')
        ->export();
    }

    /**
     * 
        $row structre:
        [0] => 1
        [1] => 王大明
        [2] => 19600612
        [3] => 10088
        [4] => 台北市
        [5] => 大安區
        [6] => 天龍路5號101樓
        [7] => 
        [8] => 
        [9] => 0912345678
        [10] => wangg@abc.com
        [11] => F
        [12] => P
        [13] => N
        [14] => 王大明-預產期: 20150808-生產醫院: 台安醫院- 8月上名單
        [15] => N
        [16] => P
        [17] => 王大明-預產期: 20150808-生產醫院: 台安醫院- 8月上名單- 台北市大安區仁愛路四段71巷17號2樓 -- 0903014591                  
     * @param   $row 
     * @return  $row
     */
    protected function encuparamPack($row)
    {
        $paramPack = new \stdClass;

        $paramPack->name          = $this->srp($row[self::IMPORT_NAME_INDEX]);
        $paramPack->state         = $this->srp($row[self::IMPORT_STATE_INDEX]);
        $paramPack->city          = $this->srp($row[self::IMPORT_CITY_INDEX]);
        $paramPack->address       = $this->srp($row[self::IMPORT_ADDRESS_INDEX], array('之', '', '', '', ''));
        $paramPack->mobile        = $this->srp($row[self::IMPORT_MOBILE_INDEX]);
        $paramPack->homeTel       = $this->srp($row[self::IMPORT_HOMETEL_INDEX]);
        $paramPack->email         = $this->srp($row[self::IMPORT_EMAIL_INDEX]);
        $paramPack->flag          = $this->srp($row[self::IMPORT_FLAG23_INDEX]);
        $paramPack->oldCustomMemo = $this->srp($row[self::IMPORT_OLDMEMO_INDEX]);
        $paramPack->newCustomMemo = $this->srp($row[self::IMPORT_NEWMEMO_INDEX]);
        
        return $paramPack;
    }

    /**
     * 移動上傳檔案至指定資料夾
     *
     * @return string $realPath
     */
    protected function moveUploadFile()
    {
        $file = Input::file('excel');
        $fileName = ExportExcel::HONEYBABY_FILENAME . $this->getStamp();
        $destinationPath = $_SERVER['DOCUMENT_ROOT'] . '/../storage/excel/import/';
        $file->move($destinationPath, $fileName);

        return $this->getImportRealPath();
    }

    /**
     * 讀取指定路徑之檔案
     *
     * 這邊原本是不打算三層包覆式的寫法，但一直發生 chunk loop 後現有檔案修改沒有被儲存的問題，
     * 導致最後修改出來的檔案只有最後一段chunk 的資料，因此百般無奈下只好改為三層callback 的寫法。
     * 我不想用原本 PHPExcel 物件來做，太難用了。
     * 
     * @param  string $realPath
     * @return array $data          
     */
    protected function loadSourceFile($realPath)
    {
        /**
         * 因為會用到 Closure，所以必須這樣宣告
         * 
         * @var App\Http\Controllers\Controller\HoneyBabyController
         */
        $self = $this;

        /**
         * 會員資料容器，包含新增檔案資料和更新檔案資料以及chunk統計
         * 
         * @var array
         */
        $data = [
            'insert'             => [], 
            'update'             => [], 
            'iterateInsertTimes' => 0,
            'iterateUpdateTimes' => 0,
            'realpath'           => $realPath
        ];

        $this->setStartMemberCode();

        // 讀取更新檔案的excel模板
        Excel::load($self->getUpdateFilePath(), function ($updatefile) use ($self, &$data) {
            // 讀取新增檔案的excel模板
            Excel::load($self->getInsertFilePath(), function ($insertfile) use ($self, &$data, &$updatefile) {
                // 讀取上傳檔案，使用 chunk 減少記憶體需求
                Excel::selectSheetsByIndex(0)                         
                    ->filter('chunk')
                    ->load($data['realpath'])
                    // 這個算是 Laravel chunk 的問題吧，會剛好在 chunk size 發生 repeat 的問題，
                    // skip 第一行就會正常了。
                    ->skip(1)                                
                    ->chunk(self::CHUNK_SIZE, function ($result) use ($self, &$data, &$updatefile, &$insertfile) {
                    
                    // 先用名字大塊豪邁撈出
                    $names = [];
                    $existMembers = [];

                    foreach ($result as $key => $row) {
                        $names[] = $self->getRowVal($row, self::IMPORT_NAME_INDEX);
                    }

                    $nameQuery = $self->getNameQuery($names);

                    if ($res = odbc_exec($self->connectToErp(), $self->cb5($nameQuery))) {
                       while ($existMembers[] = odbc_fetch_array($res));
                    }

                    // 比對會員是否存在
                    foreach ($result as $key => $row) {
                        if (false !== ($member = $self->isExist($existMembers, $row))) {
                            $data['update'][] = (array) $self->buildUpdateAppendRow($member, $row);
                        } else {
                            $data['insert'][] = (array) $self->buildInsertAppendRow($row);
                        }
                    }

                    // Append in insert
                    $insertfile->sheet('會員資料', function($sheet) use ($self, &$data) {
                        foreach ($data['insert'] as $key => $info) {
                            // +2 的原因是 
                            // 1(Excel第一行從0開始，陣列是0, 所以這邊要shift1) 
                            // + 1(略過被寫入excel模板的第一行[表頭])
                            $sheet->appendRow(
                                $self->getAppendRowIndex($key, $data['iterateInsertTimes']), 
                                $info['memberinfo']
                            );                    
                        }
                    });

                    $insertfile->sheet('會員旗標', function($sheet) use ($self, &$data) {
                        foreach ($data['insert'] as $key => $info) {
                            // +2 的原因是 
                            // 1(Excel第一行從0開始，陣列是0, 所以這邊要shift1) 
                            // + 1(略過被寫入excel模板的第一行[表頭])
                            $sheet->appendRow(
                                $self->getAppendRowIndex($key, $data['iterateInsertTimes']), 
                                $info['flag']
                            );                    
                        }
                    });

                    // Append in update
                    $updatefile->sheet('會員資料', function($sheet) use ($self, &$data) {
                        foreach ($data['update'] as $key => $info) {
                            // +2 的原因是 
                            // 1(Excel第一行從0開始，陣列是0, 所以這邊要shift1) 
                            // + 1(略過被寫入excel模板的第一行[表頭])
                            $sheet->appendRow(
                                $self->getAppendRowIndex($key, $data['iterateUpdateTimes']), 
                                $info['memberinfo']
                            );                    
                        }
                    });

                    // Append in update
                    $updatefile->sheet('會員旗標', function($sheet) use ($self, &$data) {
                        foreach ($data['update'] as $key => $info) {
                            // +2 的原因是 
                            // 1(Excel第一行從0開始，陣列是0, 所以這邊要shift1) 
                            // + 1(略過被寫入excel模板的第一行[表頭])
                            $sheet->appendRow(
                                $self->getAppendRowIndex($key, $data['iterateUpdateTimes']), 
                                $info['flag']
                            );                    
                        }
                    });

                    $data['iterateInsertTimes'] += count($data['insert']);
                    $data['iterateUpdateTimes'] += count($data['update']);

                    // release array memory
                    unset($data['update']);
                    unset($data['insert']);
                    unset($names);
                    unset($existMembers);
                    
                    $data['update'] = array();
                    $data['insert'] = array();                   
                });
            })->store('xls', storage_path('excel/exports'));
        })->store('xls', storage_path('excel/exports'));

        return $data;
    }

    /**
     * 1. 姓名 + 手機 且 手機不為空
     * 2. 姓名 + 住址 且 住址不為空
     * 3. 姓名 + 家裡電話 且 家裡電話不為空
     * 
     * @param array       $existMembers [資料庫存在的會員資料]
     * @param collection  $row          [EXCEL 資料]
     */
    protected function isExist($existMembers, $row)
    {
        foreach ($existMembers as $key => $exitstMember) {
            if (trim($this->c8($exitstMember['Name'])) !== trim($row[self::IMPORT_NAME_INDEX])) {
                continue;
            }

            if ($this->strictCompare($this->c8($exitstMember['CellPhone']), $row[self::IMPORT_MOBILE_INDEX])) {
                return $exitstMember;
            }

            if ($this->strictCompare($this->c8($exitstMember['HomeAddress_Address']), $row[self::IMPORT_ADDRESS_INDEX])) {
                return $exitstMember;
            }

            if ($this->strictCompare($this->c8($exitstMember['HomeTel']), $row[self::IMPORT_HOMETEL_INDEX])) {
                return $exitstMember;
            }
        }

        return false;
    }

    /**
     * 將多餘自元過濾後進行比對，且第一個字串不得為空，
     * 符合以上兩個條件才會傳回true
     * 
     * @param  string $str1       
     * @param  string $str2       
     * @param  array  $placeholder
     * @return boolean
     */
    protected function strictCompare($str1, $str2, $placeholder = [''])
    {
        return !empty($str1) && str_replace($this->queryReplaceWordsArray, $placeholder, $str1) === str_replace($this->queryReplaceWordsArray, $placeholder, $str2);
    }

    protected function getNameQuery(array $names)
    {
        $sql = 'SELECT Code,Name,HomeTel,OfficeTel,CellPhone,HomeAddress_State,HomeAddress_City,HomeAddress_Address FROM POS_Member WHERE Name IN (';

        foreach ($names as $key => $val) {
            $sql .= "'" . $val . "',";
        }

        $sql = substr($sql, 0, -1);
        $sql.= ')';

        return $sql;
    }

    protected function setStartMemberCode()
    {
        $query = 'SELECT TOP 1 MemberCardNo FROM POS_Member WHERE MemberCardNo LIKE \'T%\' ORDER BY Code DESC';
        
        $res = odbc_exec($this->connectToErp(), $query);
        $member = odbc_fetch_array($res);

        $this->memberCode = $member['MemberCardNo'];

        return $this;
    }

    protected function increMemberCode()
    {
        $integerPart = (int) substr($this->memberCode, 1);

        $this->memberCode = substr($this->memberCode, 0, 1) . ($integerPart + 1);

        return $this;
    }

    protected function getMemberCode()
    {
        return $this->memberCode;
    }

    /**
     * 取得目前新增 row 的正確 row index
     * 
     * @param  int    $key          [資料陣列的鍵]
     * @param  size_t $iterateTimes [chunk 的迭代次數]
     * @return int           
     */
    protected function getAppendRowIndex($key, $iterateTimes)
    {
        return $key + 2 + $iterateTimes;
    }

    /**
     * 產生貼上更新檔案的資料陣列
     * 
     * @param  array $member [輔翼DB會員資料]
     * @param  array/hook $row    [上傳檔案會員資料]
     * @return array $mixInfo
     */
    protected function buildUpdateAppendRow($member, $row)
    {
        if (!array_key_exists('Code',  $member)) {
            throw new \Exception('ERROR member info');
        }

        /**
         * 會員更新excel的資料來源陣列，
         * 產生的excel 的欄位資料全部來自該陣列。
         * 
         * @var array
         */
        $mixInfo = array();

        $mixInfo['flag23'] = $this->getRowVal($row, self::IMPORT_FLAG23_INDEX);
        
        /**
         * 會員旗標
         */
        $this
            ->genUpdateInfoValue($mixInfo, $row, $member)
            ->genUpdateFlagValue($mixInfo)
        ;

        return $mixInfo;
    }

    protected function genUpdateInfoValue(&$mixInfo, $row, $member)
    {
        $mixInfo['memberinfo'] = array();

        for ($i = 0; $i < $this->rmi('S'); $i ++) {
            $mixInfo['memberinfo'][$i] = NULL;
        }

        /**
         * 會員資料
         */
        // 會員編號
        $mixInfo['memberinfo'][$this->rmi('A')] = $member['Code'];

        // 舊客備註
        $mixInfo['memberinfo'][$this->rmi('P')] = $this->getRowVal($row, self::IMPORT_OLDMEMO_INDEX);

        return $this;
    }

    protected function genUpdateFlagValue(&$mixInfo)
    {
        if (!array_key_exists('flag23', $mixInfo)) {
            throw new \Exception('$mixinfo Key error!');
        }

        $mixInfo['flag'] = array();

        // 旗標1 => 旗標40 loop
        for ($i = 0; $i < 40; $i ++) {
            $mixInfo['flag'][$i] = NULL;
        }

        $mixInfo['flag'][0] = $mixInfo['memberinfo'][0];
        $mixInfo['flag'][4] = 'N';
        $mixInfo['flag'][5] = 'N';
        $mixInfo['flag'][23] = $mixInfo['flag23'];
        $mixInfo['flag'][38] = $this->getMonthFlag3738($this->getDate());

        return $this;
    }

    /**
     * 產生貼上新增檔案的資料陣列
     * 
     * @param  array/hook $row    [上傳檔案會員資料]
     * @return array $mixInfo      
     */
    protected function buildInsertAppendRow($row)
    {
        /**
         * 會員新增excel的資料來源陣列，
         * 產生的excel 的欄位資料全部來自該陣列。
         * 
         * @var array
         */
        $mixInfo = array();

        /**
         * 會員資料
         */
        $mixInfo['code']          = $this->increMemberCode()->getMemberCode();
        $mixInfo['name']          = $this->getRowVal($row, self::IMPORT_NAME_INDEX);
        $mixInfo['birthday']      = $this->formatBirthday($this->getRowVal($row, self::IMPORT_BIRTHDAY_INDEX));
        $mixInfo['areacode']      = $this->getRowVal($row, self::IMPORT_AREACODE_INDEX);
        $mixInfo['state']         = $this->getRowVal($row, self::IMPORT_STATE_INDEX);
        $mixInfo['city']          = $this->getRowVal($row, self::IMPORT_CITY_INDEX);
        $mixInfo['address']       = $this->getRowVal($row, self::IMPORT_ADDRESS_INDEX);
        $mixInfo['mobile']        = $this->getRowVal($row, self::IMPORT_MOBILE_INDEX);
        $mixInfo['homeTel']       = $this->getRowVal($row, self::IMPORT_HOMETEL_INDEX);
        $mixInfo['companyTel']    = $this->getRowVal($row, self::IMPORT_COMPANYTEL_INDEX);
        $mixInfo['email']         = $this->getRowVal($row, self::IMPORT_EMAIL_INDEX);
        $mixInfo['flag23']        = $this->getRowVal($row, self::IMPORT_FLAG23_INDEX);
        $mixInfo['oldCustomMemo'] = $this->getRowVal($row, self::IMPORT_OLDMEMO_INDEX);
        $mixInfo['newCustomMemo'] = $this->getRowVal($row, self::IMPORT_NEWMEMO_INDEX);

        /**
         * 會員旗標
         */
        $this
            ->genInsertFlagValue($mixInfo)
            ->genInsertMemberInfoValue($mixInfo)
        ;

        return $mixInfo;
    }

    protected function formatBirthday($birthday)
    {
        if (empty($birthday)) {
            return NULL;
        }
        
        // len(750704) === 6
        return (self::TW_DATE_LENGTH === strlen($birthday)) 
            ? (((int) substr($birthday, 0, 2)) + self::BASE_YEAR) . substr($birthday, 2, 4)
            : $birthday;
    }

    protected function genInsertMemberInfoValue(&$mixInfo)
    {
        $mixInfo['memberinfo'] = array();

        for ($i = 0; $i < $this->rmi('AP'); $i ++) {
            $mixInfo['memberinfo'][$i] = NULL;
        }

        $mixInfo['memberinfo'][self::EXPORT_INSERT_MEMBERINFO_SERNO_INDEX]         = $mixInfo['code'];
        $mixInfo['memberinfo'][self::EXPORT_INSERT_MEMBERINFO_CREATEDATE_INDEX]    = date('Ymd');
        $mixInfo['memberinfo'][self::EXPORT_INSERT_MEMBERINFO_NAME_INDEX]          = $mixInfo['name'];
        $mixInfo['memberinfo'][self::EXPORT_INSERT_MEMBERINFO_SEX_INDEX]           = '0';
        $mixInfo['memberinfo'][self::EXPORT_INSERT_MEMBERINFO_BIRTHDAY_INDEX]      = $mixInfo['birthday'];
        $mixInfo['memberinfo'][self::EXPORT_INSERT_MEMBERINFO_CATEGORYCODE_INDEX]  = self::MEMBER_CATEGORY_CODE;
        $mixInfo['memberinfo'][self::EXPORT_INSERT_MEMBERINFO_DISCODE_INDEX]       = $this->getDistincCode($this->getDate());
        $mixInfo['memberinfo'][self::EXPORT_INSERT_MEMBERINFO_LEVEL_INDEX]         = self::MEMBER_LEVEL_CODE; 
        $mixInfo['memberinfo'][self::EXPORT_INSERT_MEMBERINFO_HOMETEL_INDEX]       = $mixInfo['homeTel'];
        $mixInfo['memberinfo'][self::EXPORT_INSERT_MEMBERINFO_COMPANYTEL_INDEX]    = $mixInfo['companyTel'];
        $mixInfo['memberinfo'][self::EXPORT_INSERT_MEMBERINFO_MOBILE_INDEX]        = $mixInfo['mobile'];
        $mixInfo['memberinfo'][self::EXPORT_INSERT_MEMBERINFO_AREACODE_PREV_INDEX] = substr($mixInfo['areacode'], 0, 3);
        $mixInfo['memberinfo'][self::EXPORT_INSERT_MEMBERINFO_AREACODE_NEXT_INDEX] = (false === ($codeTail2 = substr($mixInfo['areacode'], 3, 2)) ? '' : $codeTail2);
        
        // 縣市+區+地址(路名)
        $mixInfo['memberinfo'][self::EXPORT_INSERT_MEMBERINFO_ADDRESS_INDEX]       = $mixInfo['state'] . $mixInfo['city'] . $mixInfo['address'];
        $mixInfo['memberinfo'][self::EXPORT_INSERT_MEMBERINFO_EMAIL_INDEX]         = $mixInfo['email'];
        $mixInfo['memberinfo'][self::EXPORT_INSERT_MEMBERINFO_EMPCODE_INDEX]       = self::MEMBER_EMP_CODE;
        $mixInfo['memberinfo'][self::EXPORT_INSERT_MEMBERINFO_MEMO_INDEX]          = $mixInfo['newCustomMemo'];

        return $this;
    }

    protected function genInsertFlagValue(&$mixInfo)
    {
        $mixInfo['flag'] = array();

        // 旗標1 => 旗標40 loop
        for ($i = 0; $i < 40; $i ++) {
            $mixInfo['flag'][$i] = NULL;
        }

        $mixInfo['flag'][0] = $mixInfo['code'];
        $mixInfo['flag'][4] = 'N';
        $mixInfo['flag'][5] = 'N';
        $mixInfo['flag'][8] = 'Y';
        $mixInfo['flag'][23] = $mixInfo['flag23'];
        $mixInfo['flag'][37] = $this->getMonthFlag3738($this->getDate());
        
        return $this;
    }

    /**
     * 取得會員旗標37/38
     *
     * 這邊作法很蠢我知道，不過至少可以坦個十個月
     * 這cp值還可以接受拉
     * 
     * @param  string $date [yyyymm]
     * @return string       
     */
    protected function getMonthFlag3738($date) 
    {
        $map = array(
            '201508' => 'P',
            '201509' => 'Q',
            '201510' => 'R',
            '201511' => 'S',
            '201512' => 'T',
            '201601' => 'U',
            '201602' => 'V',
            '201603' => 'W',
            '201604' => 'X',
            '201605' => 'Z'
        );

        return (array_key_exists($date, $map)) ? $map[$date] : 'FLAG_UNDEFINED';
    }

    /**
     * 取得會員區別代碼
     *
     * 這邊作法很蠢我知道，不過至少可以坦個十個月
     * 這cp值還可以接受拉
     * 
     * @param  string $date [yyyymm]
     * @return string       
     */
    protected function getDistincCode($date)
    {
        // 126-67  126-67寵兒俱樂部-104-07 
        $map = array(
            '201508' => '126-68',
            '201509' => '126-69',
            '201510' => '126-70',
            '201511' => '126-71',
            '201512' => '126-72',
            '201601' => '126-73',
            '201602' => '126-74',
            '201603' => '126-75',
            '201604' => '126-76',
            '201605' => '126-77'
        );

        return (array_key_exists($date, $map)) ? $map[$date] : 'DISTINC_UNDEFINED';
    }

    
    /**
     * 產生輔翼系統新增會員檔案
     * 
     * @return string $dest
     */
    protected function genFlapInsertFile()
    {
        $file = $_SERVER['DOCUMENT_ROOT'] . '/../storage/excel/example/insertFormat.xls';
        $dest = $this->getInsertFilePath();

        if (!copy($file, $dest)) {
            throw new \Exception('Could not copy file!');
        }

        return $this;
    }

    /**
     * 產生輔翼系統更新會員資料檔案
     * 
     * @return string $dest
     */
    protected function genFlapUpdateFile()
    {
        $file = $_SERVER['DOCUMENT_ROOT'] . '/../storage/excel/example/updateFormat.xls';
        $dest = $this->getUpdateFilePath();

        if (!\File::copy($file, $dest)) {
            throw new \Exception('Could not copy file!');
        }

        return $this;
    }

    /**
     * 取得 Upload 檔案路徑
     * 
     * @return string
     */
    protected function getImportRealPath()
    {
        return $_SERVER['DOCUMENT_ROOT'] . '/../storage/excel/import/' . $this->getImportFileName();
    }

    /**
     * 取得新增檔案路徑
     * 
     * @return string
     */
    protected function getInsertFilePath()
    {
        return  $_SERVER['DOCUMENT_ROOT'] . '/../storage/excel/exports/' . $this->getImportFileName() . '_Insert.xls';
    }

    /**
     * 取得更新檔案路徑
     * 
     * @return string
     */
    protected function getUpdateFilePath()
    {
        return  $_SERVER['DOCUMENT_ROOT'] . '/../storage/excel/exports/' . $this->getImportFileName() . '_Update.xls';
    }

    protected function setDate()
    {
        $this->date = Input::get('year') . str_pad(Input::get('month'), 2, 0, STR_PAD_LEFT);

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

    protected function getImportFileName()
    {
        return ExportExcel::HONEYBABY_FILENAME . $this->getStamp();
    }
}