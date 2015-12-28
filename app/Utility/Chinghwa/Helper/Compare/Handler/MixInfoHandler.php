<?php

namespace App\Utility\Chinghwa\Helper\Compare\Handler;

use App\Utility\Chinghwa\Helper\Flap\PosMember\MemberCode;
use App\Utility\Chinghwa\Helper\Compare\FixtureHelper;
use App\Utility\Chinghwa\Compare\HoneyBaby;
use App\Utility\Chinghwa\Helper\Excel\ExcelHelper;

class MixInfoHandler
{
	const FLAG_KEYNAME = 'flag';

    protected $memberCode;
    protected $dateString;

    public function __construct($dateString)
    {
        $this->memberCode = new MemberCode();
        $this->memberCode->setStartCode(); 

        $this->dateString = $dateString;
    }

    protected function getDateString()
    {
        return $this->dateString;
    }

    /**
     * 擴充 data 陣列內容
     *
     * 首先從資料庫根據整個chunk 的 result 取得可能名單，
     * 再逐個 row iterate 一一比對是否確實存在。
     * 
     * @param  object   $result [load src excel result]
     * @param  array    $data 
     * @return $this        
     */
    public function extendData($result, array $data)
    {
        $existMemberOrFalse = ExistHandler::fetchMightExistMembers($result);

        foreach ($result as $row) {
            if (false !== ($member = ExistHandler::isExist($existMemberOrFalse, $row))) {
                $data['update'][] = (array) $this->buildUpdateAppendRow($member, $row);
            } else if ($this->isRowhasName($row)) {
                $data['insert'][] = (array) $this->buildInsertAppendRow($row);
            }
        }

        return $data;
    }

    /**
     * src excel single row has name value or not
     * 
     * @param  object  $row
     * @return boolean     
     */
    protected function isRowhasName($row)
    {
        return isset($row[HoneyBaby::IMPORT_NAME_INDEX]) && 0 < strlen($row[HoneyBaby::IMPORT_NAME_INDEX]);
    }

	/**
     * 產生貼上新增檔案的資料陣列
     * 
     * @param  array $row    [上傳檔案會員資料]
     * @return array $mixInfo      
     */
    public function buildInsertAppendRow($row)
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
        $mixInfo['code']          = $this->memberCode->increCode()->getCode();
        $mixInfo['name']          = getRowVal($row, HoneyBaby::IMPORT_NAME_INDEX);
        $mixInfo['birthday']      = $this->formatBirthday(getRowVal($row, HoneyBaby::IMPORT_BIRTHDAY_INDEX));
        $mixInfo['areacode']      = ctype_digit($areacode = getRowVal($row, HoneyBaby::IMPORT_AREACODE_INDEX)) ? $areacode : HoneyBaby::DEFAULT_AREACODE;
        $mixInfo['state']         = getRowVal($row, HoneyBaby::IMPORT_STATE_INDEX);
        $mixInfo['city']          = getRowVal($row, HoneyBaby::IMPORT_CITY_INDEX);
        $mixInfo['address']       = getRowVal($row, HoneyBaby::IMPORT_ADDRESS_INDEX);
        $mixInfo['mobile']        = getRowVal($row, HoneyBaby::IMPORT_MOBILE_INDEX);
        $mixInfo['homeTel']       = getRowVal($row, HoneyBaby::IMPORT_HOMETEL_INDEX);
        $mixInfo['companyTel']    = getRowVal($row, HoneyBaby::IMPORT_COMPANYTEL_INDEX);
        $mixInfo['email']         = getRowVal($row, HoneyBaby::IMPORT_EMAIL_INDEX);
        $mixInfo['flag23']        = getRowVal($row, HoneyBaby::IMPORT_FLAG23_INDEX);
        $mixInfo['flag37']        = getRowVal($row, ExcelHelper::rmi('M'));
        $mixInfo['oldCustomMemo'] = getRowVal($row, HoneyBaby::IMPORT_OLDMEMO_INDEX);
        $mixInfo['newCustomMemo'] = getRowVal($row, HoneyBaby::IMPORT_NEWMEMO_INDEX);

        /**
         * 會員旗標
         */
        $this
            ->genInsertFlagValue($mixInfo)
            ->genInsertMemberInfoValue($mixInfo)
        ;

        return $mixInfo;
    }

	/**
     * 產生貼上更新檔案的資料陣列
     * 
     * @param  array $member [輔翼DB會員資料]
     * @param  array $row    [上傳檔案會員資料]
     * @return array $mixInfo
     */
    public function buildUpdateAppendRow($member, $row)
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

        $mixInfo['flag23'] = getRowVal($row, HoneyBaby::IMPORT_FLAG23_INDEX);
        $mixInfo['flag38'] = getRowVal($row, ExcelHelper::rmi('M'));
        
        /**
         * 會員旗標
         */
        $this
            ->genUpdateInfoValue($mixInfo, $row, $member)
            ->genUpdateFlagValue($mixInfo)
        ;

        return $mixInfo;
    }

	protected function genInsertFlagValue(&$mixInfo)
    {
        $this->initMixInfoEle($mixInfo, self::FLAG_KEYNAME, 40);

        $mixInfo[self::FLAG_KEYNAME][0]  = $mixInfo['code'];
        $mixInfo[self::FLAG_KEYNAME][4]  = HoneyBaby::DEFAULT_0405FLAG_VALUE;
        $mixInfo[self::FLAG_KEYNAME][5]  = HoneyBaby::DEFAULT_0405FLAG_VALUE;
        $mixInfo[self::FLAG_KEYNAME][8]  = HoneyBaby::DEFAULT_08FLAG_VALUE;
        $mixInfo[self::FLAG_KEYNAME][23] = $mixInfo['flag23'];
        $mixInfo[self::FLAG_KEYNAME][37] = $mixInfo['flag37'];
        $mixInfo[self::FLAG_KEYNAME][38] = HoneyBaby::DEFAULT_3738FLAG_VALUE;

        return $this;
    }

    protected function genUpdateInfoValue(&$mixInfo, $row, $member)
    {
        $this->initMixInfoEle($mixInfo, 'memberinfo', ExcelHelper::rmi('S'));

        /**
         * 會員資料
         */
        // 會員編號
        $mixInfo['memberinfo'][ExcelHelper::rmi('A')] = $member['Code'];

        // 舊客備註
        $mixInfo['memberinfo'][ExcelHelper::rmi('P')] = getRowVal($row, HoneyBaby::IMPORT_OLDMEMO_INDEX);

        return $this;
    }

    protected function genUpdateFlagValue(&$mixInfo)
    {
        if (!array_key_exists('flag23', $mixInfo)) {
            throw new \Exception('$mixinfo Key error!');
        }

        $this->initMixInfoEle($mixInfo, self::FLAG_KEYNAME, 40);

        $mixInfo[self::FLAG_KEYNAME][0] = $mixInfo['memberinfo'][0];
        $mixInfo[self::FLAG_KEYNAME][4] = HoneyBaby::DEFAULT_0405FLAG_VALUE;
        $mixInfo[self::FLAG_KEYNAME][5] = HoneyBaby::DEFAULT_0405FLAG_VALUE;
        $mixInfo[self::FLAG_KEYNAME][23] = $mixInfo['flag23'];
        $mixInfo[self::FLAG_KEYNAME][37] = HoneyBaby::DEFAULT_3738FLAG_VALUE;
        $mixInfo[self::FLAG_KEYNAME][38] = $mixInfo['flag38'];

        return $this;
    }

    protected function genInsertMemberInfoValue(&$mixInfo)
    {
        $this->initMixInfoEle($mixInfo, 'memberinfo', ExcelHelper::rmi('AP'));

        $mixInfo['memberinfo'][HoneyBaby::EXPORT_INSERT_MEMBERINFO_SERNO_INDEX]         = $mixInfo['code'];
        $mixInfo['memberinfo'][HoneyBaby::EXPORT_INSERT_MEMBERINFO_CREATEDATE_INDEX]    = date('Ymd');
        $mixInfo['memberinfo'][HoneyBaby::EXPORT_INSERT_MEMBERINFO_NAME_INDEX]          = $mixInfo['name'];
        $mixInfo['memberinfo'][HoneyBaby::EXPORT_INSERT_MEMBERINFO_SEX_INDEX]           = '0';
        $mixInfo['memberinfo'][HoneyBaby::EXPORT_INSERT_MEMBERINFO_BIRTHDAY_INDEX]      = $mixInfo['birthday'];
        $mixInfo['memberinfo'][HoneyBaby::EXPORT_INSERT_MEMBERINFO_CATEGORYCODE_INDEX]  = HoneyBaby::MEMBER_CATEGORY_CODE;
        $mixInfo['memberinfo'][HoneyBaby::EXPORT_INSERT_MEMBERINFO_DISCODE_INDEX]       = FixtureHelper::getDistincCode($this->getDateString());
        $mixInfo['memberinfo'][HoneyBaby::EXPORT_INSERT_MEMBERINFO_LEVEL_INDEX]         = HoneyBaby::MEMBER_LEVEL_CODE; 
        $mixInfo['memberinfo'][HoneyBaby::EXPORT_INSERT_MEMBERINFO_HOMETEL_INDEX]       = $mixInfo['homeTel'];
        $mixInfo['memberinfo'][HoneyBaby::EXPORT_INSERT_MEMBERINFO_COMPANYTEL_INDEX]    = $mixInfo['companyTel'];
        $mixInfo['memberinfo'][HoneyBaby::EXPORT_INSERT_MEMBERINFO_MOBILE_INDEX]        = $mixInfo['mobile'];
        $mixInfo['memberinfo'][HoneyBaby::EXPORT_INSERT_MEMBERINFO_AREACODE_PREV_INDEX] = substr($mixInfo['areacode'], 0, 3);
        $mixInfo['memberinfo'][HoneyBaby::EXPORT_INSERT_MEMBERINFO_AREACODE_NEXT_INDEX] = (false === ($codeTail2 = substr($mixInfo['areacode'], 3, 2)) ? '' : $codeTail2);      
        $mixInfo['memberinfo'][HoneyBaby::EXPORT_INSERT_MEMBERINFO_ADDRESS_INDEX]       = $mixInfo['state'] . $mixInfo['city'] . $mixInfo['address'];
        $mixInfo['memberinfo'][HoneyBaby::EXPORT_INSERT_MEMBERINFO_EMAIL_INDEX]         = $mixInfo['email'];
        $mixInfo['memberinfo'][HoneyBaby::EXPORT_INSERT_MEMBERINFO_EMPCODE_INDEX]       = HoneyBaby::MEMBER_EMP_CODE;
        $mixInfo['memberinfo'][HoneyBaby::EXPORT_INSERT_MEMBERINFO_MEMO_INDEX]          = $mixInfo['newCustomMemo'];

        return $this;
    }

    protected function formatBirthday($birthday)
    {
        if (empty($birthday)) {
            return NULL;
        }
        
        return (HoneyBaby::TW_DATE_LENGTH === strlen($birthday)) 
            ? (((int) substr($birthday, 0, 2)) + HoneyBaby::BASE_YEAR) . substr($birthday, 2, 4)
            : $birthday;
    }

    /**
     * 初始化 mixInfo 鍵值
     * 
     * @param  array  &$mixInfo
     * @param  string  $key     
     * @param  integer $size    
     * @return $this           
     */
    protected function initMixInfoEle(&$mixInfo, $key = 'flag', $size = 40)
    {
    	$mixInfo[$key] = [];

        for ($i = 0; $i < $size; $i ++) {
            $mixInfo[$key][$i] = NULL;
        }

        return $this;
    }
}