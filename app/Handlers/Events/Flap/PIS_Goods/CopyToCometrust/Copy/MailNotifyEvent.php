<?php

namespace App\Handlers\Events\Flap\PIS_Goods\CopyToCometrust\Copy;

use App\Events\Flap\PIS_Goods\CopyToCometrust\CopyEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Mail;

class MailNotifyEvent
{
    protected $to = [
        'vivian@chinghwa.com.tw'     => '謝玉英',
        'melodyhong@chinghwa.com.tw' => '洪鑾英',
        'pyeh@chinghwa.com.tw'       => '葉晴慧',
        'adam@chinghwa.com.tw'       => '丁東煌'
    ];

    protected $cc = [
        'jocoonopa@chinghwa.com.tw' => '洪小閎'
    ];

    protected $subject;

    public function __construct()
    {
        $this->subject = '景華商品複製為康萃特紀錄通知@' . date('Y-m-d H:i:s');
    }

    /**
     * Handle the event.
     *
     * @param  CopyEvent  $event
     * @return void
     */
    public function handle(CopyEvent $event)
    {
        Mail::send('emails.flap.PIS_Goods.copyToCt', ['title' => $this->subject, 'goodses' => $event->getGoodses()], function ($m) {
            $m->subject($this->subject)->to($this->to)->cc($this->cc);
        });

        return $this;
    }
}
