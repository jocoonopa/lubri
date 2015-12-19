<?php

namespace App\Events\Flap\PIS_Goods;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class FixCPrefixGoodsEvent extends Event
{
    use SerializesModels;

    protected $beforeDays;
    protected $targetCodes = [];
    protected $massCodesList = [];
    protected $originGoodses = [];
    protected $convertGoodses = [];

    public function __construct($number, array $codes)
    {
        $this->setBeforeDays($number)->setTargetCodes($codes);
    }

    public function setTargetCodes(array $codes)
    {
        $this->targetCodes = $codes;

        return $this;
    }

    public function setBeforeDays($number)
    {
        $this->beforeDays = $number;

        return $this;
    }

    public function setMassCodesList(array $list)
    {
        $this->massCodesList = $list;

        foreach ($this->getMassCodesList() as $massCode) {
            if (false !== ($key = array_search($massCode, $this->targetCodes))) {
                unset($this->targetCodes[$key]);
            }
        }

        return $this;
    }

    public function setOriginGoodses(array $goodses)
    {
        foreach ($goodses as $goods) {
            $this->originGoodses[$goods['SerNo']] = $goods['Code'];
        }

        return $this;
    }

    public function setConvertGoodses(array $list)
    {
        $this->convertGoodses = $list;

        return $this;
    }

    public function getMassCodesList()
    {
        return $this->massCodesList;
    }

    public function getOriginGoodses()
    {
        return $this->originGoodses;
    }

    public function getConvertGoodses()
    {
        return $this->convertGoodses;
    }

    public function getTargetCodes()
    {
        return $this->targetCodes;
    }

    public function getBeforeDays()
    {
        return $this->beforeDays;
    }
}
