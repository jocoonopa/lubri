<?php

namespace App\Utility\Chinghwa\Flap\POS_Member\Import;

use Input;

class Import extends \App\Import\Flap\POS_Member\Import
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

    const A_NAME    = 0;
    const A_EMAIL   = 1;
    const A_TEL     = 2;
    const A_ADDRESS = 3;
    const A_SEX     = 4;

    const TARGET_FLAGS = '["11", "12", "37", "38"]';

    protected $fileName;
    protected $destinationPath;

    /**
     * Used in resources\views\flap\posmember\import_task\_searchnull.blade.php:
     * 
     * @return [type] [description]
     */
    public static function getNullColumns()
    {
        return [
            'homeaddress' => '住址', 
            'state_id'    => '郵遞區號', 
            'cellphone'   => '手機', 
            'hometel'     => '住家電話', 
            'pushed_at'   => '尚未推送', 
            'birthday'    => '生日', 
            'period_at'   => '預產期', 
            'hospital'    => '醫院', 
            'email'       => '電子信箱'
        ];
    }

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