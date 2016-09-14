<?php

namespace App\Handlers\Events\Report\RetailSales;

use App\Events\Report\RetailSales\ReportEvent;
use Mail;

class MailEventHandler
{
    protected $to = [
        'mis@chinghwa.com.tw' => 'mis'
    ];

    protected $cc = [];

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
