<?php

namespace App\Handlers\Events\Report\RetailSalePersionFormula;

use App\Events\Report\RetailSalePersonFormula\ReportEvent;
use App\Utility\Chinghwa\Export\RetailSalePersonExport;
use Mail;

class MailEventHandler
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

    protected $subject;
    protected $event;

    public function __construct()
    {
        $this->setSubject('門市營業額分析月報表-人_' . date('Ymd'));
    }

    /**
     * Handle the event.
     *
     * @param  SomeEvent  $event
     * @return void
     */
    public function handle(ReportEvent $reportEvent)
    {
        $this->setEvent($reportEvent);

        return Mail::send('emails.creditCard', ['title' => $this->subject], function ($m) {
            $m
                ->to($this->to)
                ->cc($this->cc)
                ->subject($this->getSubject())
                ->attach($this->getFilePath($this->getEvent()->getExport()))
            ;
        });
    }

    protected function getFilePath(RetailSalePersonExport $export)
    {
        return __DIR__ . '/../../../../../storage/excel/exports/' . $export->getFilename() . '.xls';
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
