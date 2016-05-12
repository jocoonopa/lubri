<?php

namespace App\Handlers\Events\Report\RetailSalePerson;

use App\Events\Report\RetailSalePerson\ReportEvent;
use App\Utility\Chinghwa\Export\RetailSalePersonExport;
use Mail;

class MailEventHandler
{
    protected $to = [
        'amy@chinghwa.com.tw'          => '6221李佩蓉',
        'meganlee@chinghwa.com.tw'     => '6500李惠淑',
        'lingying3025@chinghwa.com.tw' => '6521吳俐潁'
    ];

    protected $cc = [
        'fengcheng@chinghwa.com.tw'  => '6600馮誠',
        'sl@chinghwa.com.tw'         => '6700莊淑玲',
        'swhsu@chinghwa.com.tw'      => '6800徐士偉',
        'tonyvanhsu@chinghwa.com.tw' => '6820徐士弘',
        'jocoonopa@chinghwa.com.tw'  => '6231洪小閎'
    ];

    protected $subject;
    protected $event;

    /**
     * Handle the event.
     *
     * @param  SomeEvent  $event
     * @return void
     */
    public function handle(ReportEvent $reportEvent)
    {
        $this->setEvent($reportEvent)->setSubject('門市營業額分析月報表-人_' . date('Ymd'));

        return Mail::send('emails.creditCard', ['title' => $this->subject], function ($m) {
            $m
                ->to($this->to)
                ->cc($this->cc)
                ->subject($this->getSubject())
                ->attach($this->getEvent()->getExport()->getRealpath())
            ;
        });
    }

    /**
     * Gets the value of event.
     *
     * @return mixed
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * Sets the value of event.
     *
     * @param mixed $event the event
     *
     * @return self
     */
    protected function setEvent($event)
    {
        $this->event = $event;

        return $this;
    }

    /**
     * Gets the value of subject.
     *
     * @return mixed
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Sets the value of subject.
     *
     * @param mixed $subject the subject
     *
     * @return self
     */
    protected function setSubject($subject)
    {
        $this->subject = $subject;

        return $this;
    }
}
