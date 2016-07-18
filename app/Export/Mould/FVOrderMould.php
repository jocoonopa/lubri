<?php

namespace App\Export\Mould;

class FVOrderMould extends FVMould
{
    protected $dateFields = ['OrderDate'];
    protected $intFields = ['Qty'];

    public function getRow(array $order)
    {
        $this->removeIgnoreColumn($order);

        foreach ($order as $colName => $val) {
            $order[$colName] = $this->transfer(array_get($order, $colName));
        }

        $this->convertDateVal($order)->convertIntFields($order);

        return $order;
    }
}