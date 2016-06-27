<?php

namespace App\Export\Mould;

class FVOrderMould extends FVMould
{
    public function getRow(array $order)
    {
        $this->removeIgnoreColumn($order);

        foreach ($order as $colName => $val) {
            $order[$colName] = $this->transfer(array_get($order, $colName));
        }

        return $order;
    }
}