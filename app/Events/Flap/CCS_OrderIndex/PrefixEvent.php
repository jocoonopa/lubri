<?php

namespace App\Events\Flap\CCS_OrderIndex;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class PrefixEvent extends Event
{
    use SerializesModels;

    protected $modifyOrders = [];

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(){}

    public function setModifyOrders(array $val)
    {
        $this->modifyOrders = $val;

        return $this;
    }

    public function getModifyOrders()
    {
        return $this->modifyOrders;
    }
}
