<?php

namespace App\Handlers\Events\Report\DailySaleRecord;

use App\Events\Report\DailySaleRecord\ReportEvent;
use App\Utility\Chinghwa\Export\DailySaleRecordExport;
use Mail;

class MailToManagerEventHandler
{
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
        $export = $reportEvent->getExport();

        $this->setEvent($reportEvent)->setSubject($reportEvent->getExport()->getFilename());

        return Mail::send('emails.creditCard', ['title' => $this->subject], function ($m) use ($export) {
            $m
                ->to($export->getTo())
                ->cc($export->getCc())
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
