<?php

namespace App\Handlers\Events\FV\Delay;

use App\Events\FV\Delay\ExecEvent;
use Mail;

class Notify
{
    public function handle(ExecEvent $event)
    {
        $que = $event->getQue();
        
        return Mail::send('emails.fv.delaynotify', ['que' => $que], function ($m) use ($que) {
            $m->subject('延時任務完成通知')->to([$que->creater->email => $que->creater->username]);
        });
    }
}
