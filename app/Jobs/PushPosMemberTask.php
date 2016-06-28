<?php

namespace App\Jobs;

use App\Jobs\Job;
use App\Model\Flap\PosMemberImportTask;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Mail;

class PushPosMemberTask extends Job implements SelfHandling, ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $task;
    protected $error;

    /**
     * Create a new job instance.
     *
     * @param  User  $user
     * @return void
     */
    public function __construct(PosMemberImportTask $task)
    {
        $this->setTask($task);
    }

    public function handle()
    {
        set_time_limit(0);

        $start = microtime(true);
        
        $this->_proc();

        $end = microtime(true);

        return $this->notify($this->getTask());
    }

    protected function _proc()
    {
        $this->getPusher($this->getTask())->pushTask($this->getTask());
    }

    protected function getPusher(PosMemberImportTask $task)
    {
        $pushClass = $task->kind->pusher;

        return with(new $pushClass);
    }

    protected function notify(PosMemberImportTask $task)
    {
        return Mail::send('emails.pushTask', ['task' => $task], function ($m) use ($task) {
            $m->to([$task->user->email => $task->user->username])->subject("{$task->kind->name}任務{$task->name}推送完成!");
        });
    }

    /**
     * Gets the value of task.
     *
     * @return mixed
     */
    public function getTask()
    {
        return $this->task;
    }

    /**
     * Sets the value of task.
     *
     * @param mixed $task the task
     *
     * @return self
     */
    protected function setTask($task)
    {
        $this->task = $task;

        return $this;
    }
}