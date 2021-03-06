<?php

namespace App\Console\Commands\FV\Sync;

use App\Events\FV\Delay\ExecEvent;
use App\Model\Log\FVSyncQue;
use Event;
use Illuminate\Console\Command;

class DoDelay extends Command
{
    const EXIST_SUCCESS = 0;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fv:dodelay';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Handle those delay ques';

    public function __construct()
    {
        set_time_limit(0);

        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {        
        return $this->hasNoExecuting() ? $this->proc() : self::EXIST_SUCCESS;
    }

    protected function hasNoExecuting()
    {
        return 0 === FVSyncQue::delayExecuting()->count();
    }

    protected function proc()
    {
        return $this->hasDelay() ? $this->execQue($this->fetchDelayQue())->proc() : self::EXIST_SUCCESS;
    }

    protected function hasDelay()
    {
        return 0 < FVSyncQue::delay()->count();
    }

    protected function fetchDelayQue()
    {
        return FVSyncQue::delay()->first();
    }

    protected function execQue(FVSyncQue $que)
    {
        $this->comment($que->id);

        Event::fire(new ExecEvent($que));

        return $this;
    }
}
