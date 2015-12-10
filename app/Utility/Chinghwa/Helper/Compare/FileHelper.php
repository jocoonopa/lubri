<?php

namespace App\Utility\Chinghwa\Helper\Compare;

use App\Utility\Chinghwa\ExportExcel;

class FileHelper
{
	protected $stamp;

	public function __construct($stamp)
	{
		$this->stamp = $stamp;
	}

	/**
     * 產生輔翼系統新增會員檔案
     * 
     * @return string $dest
     */
    public function genFlapInsertFile()
    {
        $file = __DIR__ . '/../../../../../storage/excel/example/insertFormat.xls';
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
    public function genFlapUpdateFile()
    {
        $file = __DIR__ . '/../../../../../storage/excel/example/updateFormat.xls';
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
    public function getImportRealPath()
    {
        return __DIR__ . '/../../../../../storage/excel/import/' . $this->getImportFileName();
    }

    /**
     * 取得新增檔案路徑
     * 
     * @return string
     */
    public function getInsertFilePath()
    {
        return __DIR__ . '/../../../../../storage/excel/exports/' . $this->getImportFileName() . '_Insert.xls';
    }

    /**
     * 取得更新檔案路徑
     * 
     * @return string
     */
    public function getUpdateFilePath()
    {
        return __DIR__ . '/../../../../../storage/excel/exports/' . $this->getImportFileName() . '_Update.xls';
    }

    public function getImportFileName()
    {
        return ExportExcel::HONEYBABY_FILENAME . $this->stamp;
    }
}