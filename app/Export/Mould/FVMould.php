<?php

namespace App\Export\Mould;

abstract class FVMould
{
    protected $ingoreHeads = ['lineNum'];
    
    abstract public function getRow(array $arr);

    protected function transfer($str)
    {
        return csvStrFilter(trim(nfTowf($str, 0)));
    }

    /**
     * 移除不必要的欄位
     * 
     * @param  array  $order 
     * @return $this      
     */
    protected function removeIgnoreColumn(&$order)
    {
        foreach ($this->ingoreHeads as $head) {
            if (array_key_exists($head, $order)) {
                unset($order[$head]);
            }            
        }

        return $this;
    }
}