<?php

namespace App\Utility\Chinghwa\Flap\POS_Member\Import;

use Input;

class Import extends \Maatwebsite\Excel\Files\ExcelFile
{
    const I_NAME      = 0;
    const I_BIRTHDAY  = 1;
    const I_ADDRESS   = 2;
    const I_ZIPCODE   = 3;
    const I_HOMETEL   = 4;
    const I_OFFICETEL = 5;
    const I_CELLPHONE = 6;
    const I_PERIOD    = 7;
    const I_EMAIL     = 8;
    const I_HOSPITAL  = 9;

    const FEMALE_SEX_CODE = 0;

    const FEMALE_SEX_TEXT = 'female';

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

    protected $fileName;
    protected $destinationPath;

    public function getFile()
    {
        $this
            ->setDestinationPath(storage_path('exports') . '/posmember/')
            ->setFileName(md5(time()) . self::FILE_EXT)
        ;

        return Input::file('file')->move($this->getDestinationPath(), $this->getFileName()) 
            ? "{$this->getDestinationPath()}{$this->getFileName()}" : NULL;
    }

    public function getFilters()
    {
        return [
            'Maatwebsite\Excel\Filters\ChunkReadFilter',
            'chunk'
        ];
    }

    /**
     * Gets the value of fileName.
     *
     * @return mixed
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * Sets the value of fileName.
     *
     * @param mixed $fileName the file name
     *
     * @return self
     */
    protected function setFileName($fileName)
    {
        $this->fileName = $fileName;

        return $this;
    }

    /**
     * Gets the value of destinationPath.
     *
     * @return mixed
     */
    public function getDestinationPath()
    {
        return $this->destinationPath;
    }

    /**
     * Sets the value of destinationPath.
     *
     * @param mixed $destinationPath the destination path
     *
     * @return self
     */
    protected function setDestinationPath($destinationPath)
    {
        $this->destinationPath = $destinationPath;

        return $this;
    }
} 