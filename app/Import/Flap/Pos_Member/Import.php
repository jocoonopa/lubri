<?php

namespace App\Import\Flap\POS_Member;

abstract class Import extends \Maatwebsite\Excel\Files\ExcelFile
{
    const FEMALE_SEX_CODE = 0;

    const FEMALE_SEX_TEXT = 'female';

    const DEFAULT_FLAG_VALUE = 'N';

    /**
     * @example '張三' 
     */
    const MINLENGTH_NAME = 2; 

    /**
     * @example '新北市寶橋路' 
     */
    const MINLENGTH_ADDRESS = 6; 

    /**
     * @example '0800527' 
     */
    const MINLENGTH_TEL = 7;  

    const WRONG_TELCODE = 'WRONG_CODE_';

    /**
     * @example '939123456' 
     */
    const MINLENGTH_CELLPHONE = 9; 

    /**
     * @example '0987654321'
     */
    const CELLPHONE_VALIDLENGTH = 10;

    /**
     * @example '231'
     */
    const MINLENGTH_ZIPCODE = 3;

    /**
     * 手機需替換開頭
     */
    const CELLPHONE_ALTERHEAD = '886';
    
    /**
     * 手機開頭第一個字元
     */
    const CELLPHONE_HEADCHAR  = '0';

    const DOC_ENCODE = 'utf-8';

    const DEFAULT_ZIPCODE      = '000';
    const DEFAULT_CITYSTATE    = '台灣省';
    
    const FILE_EXT             = '.xls';
    const CHUNK_SIZE           = 200;
    const TELCODE_HEAD         = '0';
    const EIGHT_LENGTH_TELCODE = self::TELCODE_HEAD . '2';
    const EXT_PREFIX           = '-';
    const STORAGE_PATH         = __DIR__ . '/../../../../../../storage/json/';
    const OPTIONS_DISTINCTION  = 'distinction';
    const OPTIONS_CATEGORY     = 'category';
    const OPTIONS_INSERTFLAG   = 'insertFlagString';
    const OPTIONS_UPDATEFLAG   = 'updateFlagString';
    const OPTIONS_MEMO         = 'memo';    
} 