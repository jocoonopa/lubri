<?php

namespace App\Handlers\Events\Flap\CCS_OrderIndex\PrefixEvent;

use App\Events\Flap\CCS_OrderIndex\PrefixEvent;
use Mail;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class MailNotifyEvent
{
    protected $to = [
        'migo@chinghwa.com.tw'         => '陳惠子',
        'judysu@chinghwa.com.tw'       => '蘇怡華',
        'lingying3025@chinghwa.com.tw' => '吳俐潁',
        'vivian@chinghwa.com.tw'       => '謝玉英',
        'pyeh@chinghwa.com.tw'         => '葉晴慧'
    ];

    protected $cc = [
        'tonyvanhsu@chinghwa.com.tw' => '徐士弘',
        'jeremy@chinghwa.com.tw'     => '游加恩',
        'selfindex@chinghwa.com.tw'  => '李濬帆',
        'jocoonopa@chinghwa.com.tw'  => '洪小閎'
    ];

    protected $subject = '康萃特單號修改通知' . date('Ymd H:i:s');

    /**
     * Handle the event.
     *
     * @param  SomeEvent  $event
     * @return void
     */
    public function handle(PrefixEvent $prefixEvent)
    {
        Mail::send('emails.flap.CCS_OrderIndex.prefixNotify', ['modifyOrders' => $prefixEvent->getModifyOrders()], function ($m) {
            $m->subject($this->subject)->to($this->to)->cc($this->cc);
        });  

        return $this->subject;
    }
}
