<?php

namespace App\Handlers\Events\FV\Delay;

use App\Events\FV\Delay\ExecEvent;
use App\Export\FV\Sync\Helper\ExecuteAgent;
use App\Model\Log\FVSyncQue;
use App\Model\Log\FVSyncType;
use Artisan;

class Exec
{
    protected $que;
    protected $startAt;

    public function __construct()
    {
        $this->startAt = microtime(true);
    }

    public function handle(ExecEvent $event)
    {            
        $this->setQue($event->getQue());        

        return $this->lock()->exec()->update()->isList() ? $this->repair() : NULL;
    }

    protected function exec()
    {
        ExecuteAgent::exec($this->getQue());

        return $this;
    }

    protected function isList()
    {
        return FVSyncType::ID_LIST === $this->getQue()->type_id;
    }

    protected function repair()
    {
        return Artisan::call('fv:listrep', ['--id' => $this->getQue()->id]);
    }

    protected function lock()
    {
        $this->getQue()->status_code = FVSyncQue::STATUS_DELAY_EXECUTING;
        $this->getQue()->save();

        return $this;
    }

    protected function update()
    {
        $this->getQue()->import_cost_time = microtime(true) - $this->startAt;
        $this->getQue()->status_code = FVSyncQue::STATUS_DELAY_COMPLETE;
        $this->getQue()->save();

        return $this;
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
    protected function setQue($que)
    {
        $this->que = $que;

        return $this;
    }
}
