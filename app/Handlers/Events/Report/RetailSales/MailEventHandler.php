<?php

namespace App\Handlers\Events\Report\RetailSales;

use App\Events\Report\RetailSales\ReportEvent;
use Mail;

class MailEventHandler
{
    protected $to = [
        'lingying3025@chinghwa.com.tw' => '6521吳俐穎'
    ];

    protected $cc = [
        'fengcheng@chinghwa.com.tw'  => '6600馮誠',
        'meganlee@chinghwa.com.tw'   => '6500李惠淑',
        'sl@chinghwa.com.tw'         => '6700莊淑玲',
        'swhsu@chinghwa.com.tw'      => '6800徐士偉',
        'sharon@chinghwa.com.tw'     => '6110張佳園',
        'tonyvanhsu@chinghwa.com.tw' => '6820徐士弘',
        's008@chinghwa.com.tw'       => 'S008高雄SOGO門市',
        's009@chinghwa.com.tw'       => 'S009美麗華門市',
        's013@chinghwa.com.tw'       => 'S013新光站前',
        's014@chinghwa.com.tw'       => 'S014新光台中',
        's017@chinghwa.com.tw'       => 'S017大統百貨',
        's028@chinghwa.com.tw'       => 'S028台南西門新光百貨',
        's049@chinghwa.com.tw'       => 'S049新光A8',
        's051@chinghwa.com.tw'       => 'S051漢神小巨蛋',
        'jocoonopa@chinghwa.com.tw'  => '6231小閎'
    ];

    protected $event;

    /**
     * Handle the event.
     *
     * @param  SomeEvent  $event
     * @return void
     */
    public function handle(ReportEvent $reportEvent)
    {
        $this->setEvent($reportEvent)->setSubject($reportEvent->getExport()->getSubject());

        return Mail::send('emails.creditCard', ['title' => $this->subject], $this->mail());
    }

    protected function mail()
    {
        return function ($m) {
            $m
                ->to($this->to)
                ->cc($this->cc)
                ->subject($this->getSubject())
                ->attach($this->getEvent()->getExport()->getRealpath())
            ;
        };
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
