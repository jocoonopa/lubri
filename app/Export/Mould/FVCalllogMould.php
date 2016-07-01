<?php

namespace App\Export\Mould;

class FVCalllogMould extends FVMould
{
    protected $dateFileds = ['通話開始時間', '通話結束時間'];

    public function getRow(array $list)
    {
        $this->removeIgnoreColumn($list)->convertDateVal($list);

        foreach ($list as $colName => $val) {
            $list[$colName] = $this->transfer(array_get($list, $colName));
        }

        return $list;
    }
}