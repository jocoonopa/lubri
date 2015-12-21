<?php

namespace App\Events\Flap\PIS_Goods\CopyToCometrust;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class CopyEvent extends Event
{
    use SerializesModels;

    protected $codes = [];
    protected $goodses = [];

    /**
     * Create a new event instance.
     *
     * @param array $codes
     * @return void
     */
    public function __construct(array $codes)
    {
        $this->setCodes($codes);
    }

    public function setCodes(array $codes)
    {
        $this->codes = $codes;

        return $this;
    }

    public function getCodes()
    {
        return $this->codes;
    }

    public function setGoodses(array $goodses)
    {
        $this->goodses = $goodses;

        return $this;
    }

    public function getGoodses()
    {
        return $this->goodses;
    }
}
