<?php

namespace App\Events\FV\Delay;

use App\Events\Event;
use App\Model\Log\FVSyncQue;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class ExecEvent extends Event
{
    use SerializesModels;

    protected $que;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(FVSyncQue $que)
    {
        $this->setQue($que);
    }

    /**
     * Gets the value of que.
     *
     * @return mixed
     */
    public function getQue()
    {
        return $this->que;
    }

    /**
     * Sets the value of que.
     *
     * @param mixed $que the que
     *
     * @return self
     */
    protected function setQue(FVSyncQue $que)
    {
        $this->que = $que;

        return $this;
    }
}
