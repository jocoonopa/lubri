<?php

namespace App\Export\RetailSales;

use Carbon\Carbon;
use Input;

class Export extends \Maatwebsite\Excel\Files\NewExcelFile
{
    protected $date;
    protected $isExport = false;

    public function getFilename()
    {   
        if (NULL === $this->getDate()) {
            $this->init();
        }    

        return 'RetailSales_' . $this->getDate()->format('Ymd');
    }

    protected function init()
    {
        return $this->setDate($this->injectCarbon())->setIsExport(1 === Input::get('is_export'));
    }

    protected function injectCarbon()
    {
        $dt = new Carbon(Input::get('date', Carbon::now()->subDay()->format('Y-m-d H:i:s')));

        if (NULL !== Input::get('date')) {
            $dt->modify('last day of this month');
        }

        $dt->hour = 23;
        $dt->minute = 59;
        $dt->second = 59;

        return $dt;
    }


    public function getSubject()
    {
        return '門市營業額分析日報表_' . $this->getDate()->format('Ymd');
    }

    public function getRealpath()
    {
        return __DIR__ . "/../../../storage/excel/exports/{$this->getFilename()}.xls";
    }

    /**
     * Gets the value of date.
     *
     * @return mixed
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Sets the value of date.
     *
     * @param mixed $date the date
     *
     * @return self
     */
    protected function setDate(Carbon $date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Sets the value of isExport.
     *
     * @param mixed $isExport the is export
     *
     * @return self
     */
    protected function setIsExport($isExport)
    {
        $this->isExport = $isExport;

        return $this;
    }

    /**
     * Gets the value of isExport.
     *
     * @return mixed
     */
    public function getIsExport()
    {
        return $this->isExport;
    }
}