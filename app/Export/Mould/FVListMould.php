<?php

namespace App\Export\Mould;

class FVListMould extends FVMould
{
    protected $dateFields = ['活動開始時間', '活動結束時間', '追蹤日期', '撥打時間', '名單分配日期', '最後通話時間', '最後更改時間', '建立時間'];

    public function getRow(array $list)
    {
        $this->removeIgnoreColumn($list)->convertDateVal($list);

        foreach ($list as $colName => $val) {
            $list[$colName] = $this->transfer(array_get($list, $colName));
        }

        return $list;
    }
}