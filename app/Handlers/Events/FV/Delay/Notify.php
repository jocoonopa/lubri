<?php

namespace App\Handlers\Events\FV\Delay;

use App\Events\FV\Delay\ExecEvent;
use Mail;

class Notify
{
    public function handle(ExecEvent $event)
    {  
        return $event->hasError() ? $this->errorNoti($event) : $this->completeNoti($event);
    }

    protected function completeNoti(ExecEvent $event)
    {
        return Mail::send('emails.fv.delaynotify', ['que' => $event->getQue()], function ($m) use ($event) {
            $m
                ->subject('延時任務完成通知')
                ->attach($event->getQue()->dest_file)
                ->to([$event->getQue()->creater->email => $event->getQue()->creater->username])
            ;
        });
    }

    protected function errorNoti(ExecEvent $event)
    {
        return Mail::send('emails.fv.delaywarning', ['que' => $event->getQue()], function ($m) use ($event) {
            $m
                ->subject('延時任務錯誤通知')
                ->to([$event->getQue()->creater->email => $event->getQue()->creater->username])
            ;
        });
    }
}
