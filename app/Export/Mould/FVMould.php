<?php

namespace App\Export\Mould;

abstract class FVMould
{
    abstract public function getRow(array $arr);

    protected function transfer($str)
    {
        return csvStrFilter(trim(nfTowf($str, 0)));
    }
}