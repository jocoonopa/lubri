<?php

namespace App\Http\Controllers\Compare;

use Validator;
use Input;
use App\Http\Controllers\Controller;
use App\Utility\Chinghwa\ExportExcel;
use App\Utility\Chinghwa\Compare\HoneyBaby;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class HoneyBabyController extends Controller
{
    protected $memberCode;
    protected $date;
    protected $stamp;
    protected $appendInsertClosureMemberProfile;
    protected $appendInsertClosureMemberDistFlag;
    protected $appendUpdateClosureMemberProfile;
    protected $appendUpdateClosureMemberDistFlag;

    public function index(Request $request)
    {
        return view('compare.honeybaby.index', ['title' => HoneyBaby::TITLE, 'res' => '']);
    }

    public function process(Request $request)
    {   
        set_time_limit(0);
        ini_set('memory_limit', '512M');

        $validator = Validator::make($request->all(),  ['excel' => 'required|mimes:xlsx']);

        if ($validator->fails()) {
            return view('compare.honeybaby.index', [
                'title' => HoneyBaby::TITLE, 
                'res'   => ExportExcel::VALIDATE_INVALID_MSG]
            );
        }

        $startTime = microtime(true);

        $this
            ->setDate()
            ->setStamp(uniqid())
            ->genFlapInsertFile()
            ->genFlapUpdateFile()
            ->setStartMemberCode()
        ; 
        
        $this->genFlapFiles($this->moveUploadFile());

        $endTime = microtime(true);

        return view('compare.honeybaby.index', [
            'title' => HoneyBaby::TITLE, 
            'res'   => view('compare.honeybaby.test', [
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

        $paramPack->name          = $this->srp($row[HoneyBaby::IMPORT_NAME_INDEX]);
        $paramPack->state         = $this->srp($row[HoneyBaby::IMPORT_STATE_INDEX]);
        $paramPack->city          = $this->srp($row[HoneyBaby::IMPORT_CITY_INDEX]);
        $paramPack->address       = $this->srp($row[HoneyBaby::IMPORT_ADDRESS_INDEX], [ExportExcel::ADDRESS_REPLACE_CHAR, '', '', '', '']);
        $paramPack->mobile        = $this->srp($row[HoneyBaby::IMPORT_MOBILE_INDEX]);
        $paramPack->homeTel       = $this->srp($row[HoneyBaby::IMPORT_HOMETEL_INDEX]);
        $paramPack->email         = $this->srp($row[HoneyBaby::IMPORT_EMAIL_INDEX]);
        $paramPack->flag          = $this->srp($row[HoneyBaby::IMPORT_FLAG23_INDEX]);
        $paramPack->oldCustomMemo = $this->srp($row[HoneyBaby::IMPORT_OLDMEMO_INDEX]);
        $paramPack->newCustomMemo = $this->srp($row[HoneyBaby::IMPORT_NEWMEMO_INDEX]);
        
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

    protected function genFlapFiles($realPath)
    {
        $data = $this->getDataPrototype($realPath);
        
        $excel = \App::make('excel');
        $insertFile = $excel->load($this->getInsertFilePath());
        $updateFile = $excel->load($this->getUpdateFilePath());

        $this
            ->setAppendInsertMemberProfileSheet($data)
            ->setAppendUpdateMemberProfileSheet($data)
            ->setAppendInsertMemberDistFlagSheet($data)
            ->setAppendUpdateMemberDistFlagSheet($data)
        ;

        $this->injectInsertAndUpdateData($data, $insertFile, $updateFile);

        $insertFile->store('xls', storage_path('excel/exports'));
        $updateFile->store('xls', storage_path('excel/exports'));

        return $this;
    }

    protected function injectInsertAndUpdateData(&$data, &$insertFile, &$updateFile)
    {
        return Excel::selectSheetsByIndex(0)
            ->filter('chunk')
            ->load($data['realpath'])
            ->skip(HoneyBaby::SKIP_LARAVEL_EXCEL_CHUNK_BUG_INDEX)
            ->chunk(HoneyBaby::CHUNK_SIZE, $this->getChunkProcess($data, $insertFile, $updateFile));
    }

    protected function getChunkProcess(&$data, &$insertFile, &$updateFile)
    {
        return function ($result) use (&$data, &$insertFile, &$updateFile) {
            $this->extendData($result, $data);

            $insertFile->sheet(HoneyBaby::SHEETNAME_MEMBER_PROFILE, $this->getAppendInsertClosureMemberProfile());
            $insertFile->sheet(HoneyBaby::SHEETNAME_MEMBER_DISTFLAG, $this->getAppendInsertClosureMemberDistFlag());

            $updateFile->sheet(HoneyBaby::SHEETNAME_MEMBER_PROFILE, $this->getAppendUpdateClosureMemberProfile());
            $updateFile->sheet(HoneyBaby::SHEETNAME_MEMBER_DISTFLAG, $this->getAppendUpdateClosureMemberDistFlag());
            
            return $this->freeVariables($data);          
        };
    }

    protected function extendData($result, &$data)
    {
        $names = []; 
        $existMembers = [];

        foreach ($result as $row) {
            $names[] = $this->getRowVal($row, HoneyBaby::IMPORT_NAME_INDEX);
        }

        $nameQuery = $this->getNameQuery($names);

        if ($res = odbc_exec($this->connectToErp(), $this->cb5($nameQuery))) {
           while ($existMembers[] = odbc_fetch_array($res));
        }

        // 比對會員是否存在
        foreach ($result as $row) {
            if (false !== ($member = $this->isExist($existMembers, $row))) {
                $data['update'][] = (array) $this->buildUpdateAppendRow($member, $row);
            } else {
                $data['insert'][] = (array) $this->buildInsertAppendRow($row);
            }
        }

        return $this;
    }

    protected function getDataPrototype($realPath)
    {
        /**
         * 會員資料容器，包含新增檔案資料和更新檔案資料以及chunk統計
         * 
         * @var array
         */
        return $data = [
            'insert'             => [], 
            'update'             => [], 
            'iterateInsertTimes' => 0,
            'iterateUpdateTimes' => 0,
            'realpath'           => $realPath
        ];
    }

    protected function freeVariables(&$data)
    {
        $data['iterateInsertTimes'] += count($data['insert']);
        $data['iterateUpdateTimes'] += count($data['update']);

        // release array memory
        unset($data['update']);
        unset($data['insert']);
        
        $data['update'] = [];
        $data['insert'] = [];         

        return $this;
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
            if (trim($this->c8($exitstMember['Name'])) !== trim($row[HoneyBaby::IMPORT_NAME_INDEX])) {
                continue;
            }

            if ($this->strictCompare($this->c8($exitstMember['CellPhone']), $row[HoneyBaby::IMPORT_MOBILE_INDEX])) {
                return $exitstMember;
            }

            if ($this->strictCompare($this->c8($exitstMember['HomeAddress_Address']), $row[HoneyBaby::IMPORT_ADDRESS_INDEX])) {
                return $exitstMember;
            }

            if ($this->strictCompare($this->c8($exitstMember['HomeTel']), $row[HoneyBaby::IMPORT_HOMETEL_INDEX])) {
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
        return !empty(str_replace($this->queryReplaceWordsArray, $placeholder, $str1)) && str_replace($this->queryReplaceWordsArray, $placeholder, $str1) === str_replace($this->queryReplaceWordsArray, $placeholder, $str2);
    }

    protected function getNameQuery(array $names)
    {
        $inString = $this->genQueryInConditionString($names);

        $sql = "SELECT Code,Name,HomeTel,OfficeTel,CellPhone,HomeAddress_State,HomeAddress_City,HomeAddress_Address FROM POS_Member WHERE Name IN ({$inString})";

        return $sql;
    }

    protected function genQueryInConditionString(array $names)
    {
        $inString = '';
        foreach ($names as $key => $val) {
            $inString .= "'{$val}',";
        }

        return substr($inString, 0, -1);
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
        $mixInfo = [];

        $mixInfo['flag23'] = $this->getRowVal($row, HoneyBaby::IMPORT_FLAG23_INDEX);
        $mixInfo['flag38'] = $this->getRowVal($row, $this->rmi('M'));
        
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
        $mixInfo['memberinfo'] = [];

        for ($i = 0; $i < $this->rmi('S'); $i ++) {
            $mixInfo['memberinfo'][$i] = NULL;
        }

        /**
         * 會員資料
         */
        // 會員編號
        $mixInfo['memberinfo'][$this->rmi('A')] = $member['Code'];

        // 舊客備註
        $mixInfo['memberinfo'][$this->rmi('P')] = $this->getRowVal($row, HoneyBaby::IMPORT_OLDMEMO_INDEX);

        return $this;
    }

    protected function genUpdateFlagValue(&$mixInfo)
    {
        if (!array_key_exists('flag23', $mixInfo)) {
            throw new \Exception('$mixinfo Key error!');
        }

        $mixInfo['flag'] = [];

        // 旗標1 => 旗標40 loop
        for ($i = 0; $i < 40; $i ++) {
            $mixInfo['flag'][$i] = NULL;
        }

        $mixInfo['flag'][0] = $mixInfo['memberinfo'][0];
        $mixInfo['flag'][4] = HoneyBaby::DEFAULT_0405FLAG_VALUE;
        $mixInfo['flag'][5] = HoneyBaby::DEFAULT_0405FLAG_VALUE;
        $mixInfo['flag'][23] = $mixInfo['flag23'];
        $mixInfo['flag'][37] = HoneyBaby::DEFAULT_3738FLAG_VALUE;
        $mixInfo['flag'][38] = $mixInfo['flag38'];

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
        $mixInfo = [];

        /**
         * 會員資料
         */
        $mixInfo['code']          = $this->increMemberCode()->getMemberCode();
        $mixInfo['name']          = $this->getRowVal($row, HoneyBaby::IMPORT_NAME_INDEX);
        $mixInfo['birthday']      = $this->formatBirthday($this->getRowVal($row, HoneyBaby::IMPORT_BIRTHDAY_INDEX));
        $mixInfo['areacode']      = ctype_digit($areacode = $this->getRowVal($row, HoneyBaby::IMPORT_AREACODE_INDEX)) ? $areacode : HoneyBaby::DEFAULT_AREACODE;
        $mixInfo['state']         = $this->getRowVal($row, HoneyBaby::IMPORT_STATE_INDEX);
        $mixInfo['city']          = $this->getRowVal($row, HoneyBaby::IMPORT_CITY_INDEX);
        $mixInfo['address']       = $this->getRowVal($row, HoneyBaby::IMPORT_ADDRESS_INDEX);
        $mixInfo['mobile']        = $this->getRowVal($row, HoneyBaby::IMPORT_MOBILE_INDEX);
        $mixInfo['homeTel']       = $this->getRowVal($row, HoneyBaby::IMPORT_HOMETEL_INDEX);
        $mixInfo['companyTel']    = $this->getRowVal($row, HoneyBaby::IMPORT_COMPANYTEL_INDEX);
        $mixInfo['email']         = $this->getRowVal($row, HoneyBaby::IMPORT_EMAIL_INDEX);
        $mixInfo['flag23']        = $this->getRowVal($row, HoneyBaby::IMPORT_FLAG23_INDEX);
        $mixInfo['flag37']        = $this->getRowVal($row, $this->rmi('M'));
        $mixInfo['oldCustomMemo'] = $this->getRowVal($row, HoneyBaby::IMPORT_OLDMEMO_INDEX);
        $mixInfo['newCustomMemo'] = $this->getRowVal($row, HoneyBaby::IMPORT_NEWMEMO_INDEX);

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
        return (HoneyBaby::TW_DATE_LENGTH === strlen($birthday)) 
            ? (((int) substr($birthday, 0, 2)) + HoneyBaby::BASE_YEAR) . substr($birthday, 2, 4)
            : $birthday;
    }

    protected function genInsertMemberInfoValue(&$mixInfo)
    {
        $mixInfo['memberinfo'] = [];

        for ($i = 0; $i < $this->rmi('AP'); $i ++) {
            $mixInfo['memberinfo'][$i] = NULL;
        }

        $mixInfo['memberinfo'][HoneyBaby::EXPORT_INSERT_MEMBERINFO_SERNO_INDEX]         = $mixInfo['code'];
        $mixInfo['memberinfo'][HoneyBaby::EXPORT_INSERT_MEMBERINFO_CREATEDATE_INDEX]    = date('Ymd');
        $mixInfo['memberinfo'][HoneyBaby::EXPORT_INSERT_MEMBERINFO_NAME_INDEX]          = $mixInfo['name'];
        $mixInfo['memberinfo'][HoneyBaby::EXPORT_INSERT_MEMBERINFO_SEX_INDEX]           = '0';
        $mixInfo['memberinfo'][HoneyBaby::EXPORT_INSERT_MEMBERINFO_BIRTHDAY_INDEX]      = $mixInfo['birthday'];
        $mixInfo['memberinfo'][HoneyBaby::EXPORT_INSERT_MEMBERINFO_CATEGORYCODE_INDEX]  = HoneyBaby::MEMBER_CATEGORY_CODE;
        $mixInfo['memberinfo'][HoneyBaby::EXPORT_INSERT_MEMBERINFO_DISCODE_INDEX]       = $this->getDistincCode($this->getDate());
        $mixInfo['memberinfo'][HoneyBaby::EXPORT_INSERT_MEMBERINFO_LEVEL_INDEX]         = HoneyBaby::MEMBER_LEVEL_CODE; 
        $mixInfo['memberinfo'][HoneyBaby::EXPORT_INSERT_MEMBERINFO_HOMETEL_INDEX]       = $mixInfo['homeTel'];
        $mixInfo['memberinfo'][HoneyBaby::EXPORT_INSERT_MEMBERINFO_COMPANYTEL_INDEX]    = $mixInfo['companyTel'];
        $mixInfo['memberinfo'][HoneyBaby::EXPORT_INSERT_MEMBERINFO_MOBILE_INDEX]        = $mixInfo['mobile'];
        $mixInfo['memberinfo'][HoneyBaby::EXPORT_INSERT_MEMBERINFO_AREACODE_PREV_INDEX] = substr($mixInfo['areacode'], 0, 3);
        $mixInfo['memberinfo'][HoneyBaby::EXPORT_INSERT_MEMBERINFO_AREACODE_NEXT_INDEX] = (false === ($codeTail2 = substr($mixInfo['areacode'], 3, 2)) ? '' : $codeTail2);
        
        // 縣市+區+地址(路名)
        $mixInfo['memberinfo'][HoneyBaby::EXPORT_INSERT_MEMBERINFO_ADDRESS_INDEX]       = $mixInfo['state'] . $mixInfo['city'] . $mixInfo['address'];
        $mixInfo['memberinfo'][HoneyBaby::EXPORT_INSERT_MEMBERINFO_EMAIL_INDEX]         = $mixInfo['email'];
        $mixInfo['memberinfo'][HoneyBaby::EXPORT_INSERT_MEMBERINFO_EMPCODE_INDEX]       = HoneyBaby::MEMBER_EMP_CODE;
        $mixInfo['memberinfo'][HoneyBaby::EXPORT_INSERT_MEMBERINFO_MEMO_INDEX]          = $mixInfo['newCustomMemo'];

        return $this;
    }

    protected function genInsertFlagValue(&$mixInfo)
    {
        $mixInfo['flag'] = [];

        // 旗標1 => 旗標40 loop
        for ($i = 0; $i < 40; $i ++) {
            $mixInfo['flag'][$i] = NULL;
        }

        $mixInfo['flag'][0]  = $mixInfo['code'];
        $mixInfo['flag'][4]  = HoneyBaby::DEFAULT_0405FLAG_VALUE;
        $mixInfo['flag'][5]  = HoneyBaby::DEFAULT_0405FLAG_VALUE;
        $mixInfo['flag'][8]  = HoneyBaby::DEFAULT_08FLAG_VALUE;
        $mixInfo['flag'][23] = $mixInfo['flag23'];
        $mixInfo['flag'][37] = $mixInfo['flag37'];
        $mixInfo['flag'][38] = HoneyBaby::DEFAULT_3738FLAG_VALUE;

        return $this;
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

    protected function setAppendInsertMemberProfileSheet(&$data)
    {
        $this->appendInsertClosureMemberProfile = function($sheet) use (&$data) {
            $this->appendRow($sheet, $data['insert'], $data['iterateInsertTimes'], 'memberinfo');
        };

        return $this;
    }

    protected function setAppendInsertMemberDistFlagSheet(&$data)
    {
        $this->appendInsertClosureMemberDistFlag = function($sheet) use (&$data) {
            $this->appendRow($sheet, $data['insert'], $data['iterateInsertTimes'], 'flag');
        };

        return $this;
    }

    protected function setAppendUpdateMemberProfileSheet(&$data)
    {
        $this->appendUpdateClosureMemberProfile = function($sheet) use (&$data) {
            $this->appendRow($sheet, $data['update'], $data['iterateUpdateTimes'], 'memberinfo');
        };

        return $this;
    }

    protected function setAppendUpdateMemberDistFlagSheet(&$data)
    {
        $this->appendUpdateClosureMemberDistFlag = function($sheet) use (&$data) {
            $this->appendRow($sheet, $data['update'], $data['iterateUpdateTimes'], 'flag');
        };

        return $this;
    }

    protected function appendRow(&$sheet, array $data, $point, $srcKey)
    {
        foreach ($data as $key => $info) {
            $sheet->appendRow($this->getAppendRowIndex($key, $point), $info[$srcKey]);                    
        }

        return $this;
    }

    protected function getAppendInsertClosureMemberProfile()
    {
        return $this->appendInsertClosureMemberProfile;
    }

    protected function getAppendInsertClosureMemberDistFlag()
    {
        return $this->appendInsertClosureMemberDistFlag;
    }

    protected function getAppendUpdateClosureMemberProfile()
    {
        return $this->appendUpdateClosureMemberProfile;
    }

    protected function getAppendUpdateClosureMemberDistFlag()
    {
        return $this->appendUpdateClosureMemberDistFlag;
    }
}