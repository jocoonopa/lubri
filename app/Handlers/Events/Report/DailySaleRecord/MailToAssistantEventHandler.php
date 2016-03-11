<?php

namespace App\Handlers\Events\Report\DailySaleRecord;

use App\Events\Report\DailySaleRecord\ReportEvent;
use App\Utility\Chinghwa\Export\DailySaleRecordExport;
use Mail;

class MailToAssistantEventHandler
{
    protected $to = [
        'oliver@chinghwa.com.tw' => '6210王誌遠',
        'selfindex@chinghwa.com.tw' => '6810李濬帆'   
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
        $this->setEvent($reportEvent)->setSubject($reportEvent->getExport()->getFilename());

        return Mail::send('emails.toam', ['title' => $this->subject], function ($m) {
            $m
                ->to($this->to)
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
