<?php

namespace App\Export\Mould;

abstract class FVMould
{
    protected $ingoreHeads = ['lineNum'];
    protected $dateFileds = [];
    
    abstract public function getRow(array $arr);

    protected function transfer($str)
    {
        return csvStrFilter(trim(nfTowf($str, 0)));
    }

    /**
     * 移除不必要的欄位
     * 
     * @param  array  $data 
     * @return $this      
     */
    protected function removeIgnoreColumn(&$data)
    {
        foreach ($this->ingoreHeads as $head) {
            if (array_key_exists($head, $data)) {
                unset($data[$head]);
            }            
        }

        return $this;
    }

    /**
     * 將日期轉換為 Y/m/d 格式
     * 
     * @param  array $data
     * @return string       
     */
    protected function convertDateVal(&$data)
    {
        foreach ($this->dateFileds as $dateField) {
            $data[$dateField] = with(new \DateTime($data[$dateField]))->format('Y/m/d H:i:s');
        }

        return $this;
    }

    protected function convertDate($date)
    {
        return with(new \DateTime($date))->format('Y/m/d H:i:s'); // 2016/07/11 20:00:00
    }
}