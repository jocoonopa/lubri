<?php

namespace App\Export\Mould;

class FVListMould extends FVMould
{
	const TRACE_DATE_COLUMN = '追蹤日期';

    protected $dateFields = ['活動開始時間', '活動結束時間', self::TRACE_DATE_COLUMN, '撥打時間', '名單分配日期', '最後通話時間', '最後更改時間', '建立時間'];

    public function getRow(array $list)
    {
    	$traceDate = array_get($dateFields,  self::TRACE_DATE_COLUMN);
        
        $this->removeIgnoreColumn($list)->convertDateVal($list);

        foreach ($list as $colName => $val) {
            $list[$colName] = $this->transfer(array_get($list, $colName));
        }

        if (empty($traceDate)) {
        	$list[ self::TRACE_DATE_COLUMN] = '';
        }

        return $list;
    }
}